<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\InvoiceRequest;
use App\Mail\InvoiceCreated;
use App\Mail\InvoiceReminder;
use App\Models\Invoice;
use App\Models\Tenant;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;

class InvoiceController extends Controller
{
    public const STATUS_LABELS = [
        'unpaid'  => 'Belum Bayar',
        'pending' => 'Menunggu Verifikasi',
        'paid'    => 'Lunas',
        'overdue' => 'Terlambat',
    ];

    public function index(Request $request)
    {
        // Auto-update status overdue untuk tagihan lewat jatuh tempo
        Invoice::where('status', 'unpaid')
            ->where('due_date', '<', now()->toDateString())
            ->update(['status' => 'overdue']);

        $query = Invoice::with(['tenant.room'])
            ->when($request->filled('month'), fn ($q) => $q->where('month', $request->month))
            ->when($request->filled('year'), fn ($q) => $q->where('year', $request->year))
            ->when($request->filled('status'), fn ($q) => $q->where('status', $request->status))
            ->when($request->filled('search'), function ($q) use ($request) {
                $s = $request->search;
                $q->where(function ($q) use ($s) {
                    $q->where('invoice_number', 'like', "%{$s}%")
                      ->orWhereHas('tenant', fn ($t) => $t->where('full_name', 'like', "%{$s}%"))
                      ->orWhereHas('tenant.room', fn ($r) => $r->where('room_number', 'like', "%{$s}%"));
                });
            })
            ->orderByDesc('year')
            ->orderByDesc('month')
            ->orderByDesc('id');

        $invoices = $query->paginate(15)->withQueryString();

        $stats = [
            'total_this_month' => Invoice::where('month', now()->month)->where('year', now()->year)->count(),
            'total_paid'       => Invoice::where('status', 'paid')->count(),
            'total_pending'    => Invoice::where('status', 'pending')->count(),
            'total_overdue'    => Invoice::where('status', 'overdue')->count(),
            'amount_this_month'=> Invoice::where('month', now()->month)->where('year', now()->year)->sum('amount'),
            'amount_paid'      => Invoice::where('status', 'paid')->sum('amount'),
            'amount_overdue'   => Invoice::where('status', 'overdue')->sum('amount'),
        ];

        $statusLabels = self::STATUS_LABELS;
        $years = range(now()->year - 2, now()->year + 1);
        $months = collect(range(1, 12))->mapWithKeys(fn ($m) => [$m => Carbon::create()->month($m)->translatedFormat('F')])->all();

        return view('admin.invoices.index', compact('invoices', 'stats', 'statusLabels', 'years', 'months'));
    }

    public function show(Invoice $invoice)
    {
        $invoice->load(['tenant.room.property', 'tenant.user', 'payments.verifier']);

        return view('admin.invoices.show', compact('invoice'));
    }

    public function create()
    {
        $tenants = Tenant::with('room.property')
            ->where('status', 'active')
            ->orderBy('full_name', 'asc')
            ->get();

        $months = collect(range(1, 12))->mapWithKeys(fn ($m) => [$m => Carbon::create()->month($m)->translatedFormat('F')])->all();
        $years = range(now()->year - 2, now()->year + 1);

        return view('admin.invoices.create', compact('tenants', 'months', 'years'));
    }

    public function store(InvoiceRequest $request)
    {
        $data = $request->validated();

        $exists = Invoice::where('tenant_id', $data['tenant_id'])
            ->where('month', $data['month'])
            ->where('year', $data['year'])
            ->exists();

        if ($exists) {
            return back()->withInput()->withErrors([
                'month' => 'Tagihan untuk penghuni ini di bulan & tahun tersebut sudah ada.',
            ]);
        }

        $invoice = null;

        DB::transaction(function () use ($data, &$invoice) {
            $tenant = Tenant::findOrFail($data['tenant_id']);
            $amount = $data['amount'] ?? $tenant->room->price;

            $invoice = Invoice::create([
                'tenant_id'      => $tenant->id,
                'invoice_number' => Invoice::generateNumber($data['year'], $data['month']),
                'month'          => $data['month'],
                'year'           => $data['year'],
                'amount'         => $amount,
                'due_date'       => $data['due_date'],
                'status'         => 'unpaid',
            ]);
        });

        // Kirim email notifikasi
        try {
            if ($invoice && $invoice->tenant->user?->email) {
                Mail::to($invoice->tenant->user->email)->send(new InvoiceCreated($invoice));
            }
        } catch (\Exception $e) {
            logger()->error('Gagal kirim InvoiceCreated: ' . $e->getMessage());
        }

        return redirect()->route('admin.invoices.index')
            ->with('success', 'Tagihan berhasil dibuat.');
    }

    public function destroy(Invoice $invoice)
    {
        if ($invoice->payments()->exists()) {
            return back()->with('error', 'Tagihan tidak bisa dihapus karena sudah ada pembayaran.');
        }

        $invoice->delete();

        return redirect()->route('admin.invoices.index')
            ->with('success', 'Tagihan berhasil dihapus.');
    }

    public function generate(Request $request)
    {
        $validated = $request->validate([
            'month' => ['required', 'integer', 'min:1', 'max:12'],
            'year'  => ['required', 'integer', 'min:2020', 'max:2100'],
        ]);

        $month = (int) $validated['month'];
        $year = (int) $validated['year'];

        $tenants = Tenant::with('room')->where('status', 'active')->get();
        $created = 0;
        $skipped = 0;
        $invoicesToEmail = [];

        DB::transaction(function () use ($tenants, $month, $year, &$created, &$skipped, &$invoicesToEmail) {
            foreach ($tenants as $tenant) {
                $exists = Invoice::where('tenant_id', $tenant->id)
                    ->where('month', $month)
                    ->where('year', $year)
                    ->exists();

                if ($exists) {
                    $skipped++;
                    continue;
                }

                $dueDate = Carbon::create($year, $month, 10);
                if ($dueDate->isPast() && $dueDate->month === now()->month && $dueDate->year === now()->year) {
                    $dueDate = now()->addDays(5);
                }

                $invoice = Invoice::create([
                    'tenant_id'      => $tenant->id,
                    'invoice_number' => Invoice::generateNumber($year, $month),
                    'month'          => $month,
                    'year'           => $year,
                    'amount'         => $tenant->room->price,
                    'due_date'       => $dueDate,
                    'status'         => 'unpaid',
                ]);

                $invoicesToEmail[] = $invoice->load('tenant.user');
                $created++;
            }
        });

        // Kirim email ke semua invoice baru (di luar transaction biar ga block)
        foreach ($invoicesToEmail as $invoice) {
            try {
                if ($invoice->tenant->user?->email) {
                    Mail::to($invoice->tenant->user->email)->send(new InvoiceCreated($invoice));
                }
            } catch (\Exception $e) {
                logger()->error('Gagal kirim InvoiceCreated (generate): ' . $e->getMessage());
            }
        }

        return redirect()->route('admin.invoices.index')
            ->with('success', "Generate selesai: {$created} tagihan dibuat, {$skipped} dilewati (sudah ada).");
    }

    public function sendReminder(Invoice $invoice)
    {
        if ($invoice->status === 'paid') {
            return back()->with('error', 'Tagihan ini sudah lunas.');
        }

        $email = $invoice->tenant->user?->email;
        if (! $email) {
            return back()->with('error', 'Penghuni tidak punya email terdaftar.');
        }

        try {
            Mail::to($email)->send(new InvoiceReminder($invoice));
            return back()->with('success', "Reminder terkirim ke {$invoice->tenant->full_name}.");
        } catch (\Exception $e) {
            logger()->error('Gagal kirim InvoiceReminder: ' . $e->getMessage());
            return back()->with('error', 'Gagal kirim email: ' . $e->getMessage());
        }
    }

    public function export(Request $request)
    {
        $query = Invoice::with(['tenant.room'])
            ->when($request->filled('month'), fn ($q) => $q->where('month', $request->month))
            ->when($request->filled('year'), fn ($q) => $q->where('year', $request->year))
            ->when($request->filled('status'), fn ($q) => $q->where('status', $request->status))
            ->orderByDesc('year')
            ->orderByDesc('month');

        $invoices = $query->get();
        $total = $invoices->sum('amount');

        $pdf = Pdf::loadView('admin.invoices.pdf', compact('invoices', 'total'))
            ->setPaper('a4', 'landscape');

        $filename = 'tagihan-' . now()->format('Ymd-His') . '.pdf';

        return $pdf->download($filename);
    }
}