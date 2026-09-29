<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\ExtensionRequest;
use App\Models\Extension;
use App\Models\Invoice;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ExtensionController extends Controller
{
    public const STATUS_LABELS = [
        'pending'  => 'Menunggu',
        'approved' => 'Disetujui',
        'rejected' => 'Ditolak',
    ];

    public function index(Request $request)
    {
        $query = Extension::with(['tenant.room.property'])
            ->when($request->filled('status'), fn ($q) => $q->where('status', $request->status))
            ->when($request->filled('search'), function ($q) use ($request) {
                $s = $request->search;
                $q->where(function ($q) use ($s) {
                    $q->whereHas('tenant', fn ($t) => $t->where('full_name', 'like', "%{$s}%"))
                      ->orWhereHas('tenant.room', fn ($r) => $r->where('room_number', 'like', "%{$s}%"));
                });
            })
            ->orderByRaw("FIELD(status, 'pending', 'approved', 'rejected')")
            ->orderByDesc('created_at');

        $extensions = $query->paginate(15)->withQueryString();

        $stats = [
            'total'    => Extension::count(),
            'pending'  => Extension::where('status', 'pending')->count(),
            'approved' => Extension::where('status', 'approved')->count(),
            'rejected' => Extension::where('status', 'rejected')->count(),
        ];

        $statusLabels = self::STATUS_LABELS;

        return view('admin.extensions.index', compact('extensions', 'stats', 'statusLabels'));
    }

    public function show(Extension $extension)
    {
        $extension->load(['tenant.user', 'tenant.room.property']);

        $statusLabels = self::STATUS_LABELS;

        return view('admin.extensions.show', compact('extension', 'statusLabels'));
    }

    /**
     * Approve perpanjangan:
     * 1. Update extension jadi approved + approved_at
     * 2. Update tenant.end_date = requested_end_date
     * 3. Generate invoice tambahan untuk durasi perpanjangan
     */
    public function approve(Request $request, Extension $extension)
    {
        if ($extension->status !== 'pending') {
            return back()->with('error', 'Pengajuan ini sudah diproses sebelumnya.');
        }

        $validated = $request->validate([
            'admin_notes' => ['nullable', 'string', 'max:500'],
        ]);

        $invoiceNumber = null;

        DB::transaction(function () use ($extension, $validated, &$invoiceNumber) {
            $tenant = $extension->tenant;

            // 1) Update extension
            $extension->update([
                'status'      => 'approved',
                'approved_at' => now(),
                'admin_notes' => $validated['admin_notes'] ?? $extension->admin_notes,
            ]);

            // 2) Update tenant end_date
            $tenant->update(['end_date' => $extension->requested_end_date]);

            // 3) Generate invoice tambahan untuk durasi perpanjangan
            $startPeriod = $extension->current_end_date->copy()->addDay();
            $year  = $startPeriod->year;
            $month = $startPeriod->month;

            // Cek duplikat invoice untuk periode ini (kalau sudah ada, skip)
            $exists = Invoice::where('tenant_id', $tenant->id)
                ->where('year', $year)
                ->where('month', $month)
                ->exists();

            if (! $exists) {
                $invoice = Invoice::create([
                    'tenant_id'      => $tenant->id,
                    'invoice_number' => Invoice::generateNumber($year, $month),
                    'month'          => $month,
                    'year'           => $year,
                    'amount'         => $extension->additional_cost,
                    'due_date'       => now()->addDays(7),
                    'status'         => 'unpaid',
                ]);

                $invoiceNumber = $invoice->invoice_number;
            }
        });

        $message = 'Perpanjangan disetujui. Tanggal selesai sewa diperbarui.';
        if ($invoiceNumber) {
            $message .= " Invoice tambahan {$invoiceNumber} telah dibuat.";
        } else {
            $message .= ' (Invoice periode tersebut sudah ada, tidak dibuat duplikat.)';
        }

        return redirect()->route('admin.extensions.show', $extension)
            ->with('success', $message);
    }

    /**
     * Reject perpanjangan dengan alasan wajib.
     */
    public function reject(Request $request, Extension $extension)
    {
        if ($extension->status !== 'pending') {
            return back()->with('error', 'Pengajuan ini sudah diproses sebelumnya.');
        }

        $validated = $request->validate([
            'admin_notes' => ['required', 'string', 'max:500'],
        ], [
            'admin_notes.required' => 'Alasan penolakan wajib diisi.',
        ]);

        $extension->update([
            'status'      => 'rejected',
            'approved_at' => now(),
            'admin_notes' => $validated['admin_notes'],
        ]);

        return redirect()->route('admin.extensions.show', $extension)
            ->with('success', 'Perpanjangan ditolak.');
    }

    public function destroy(Extension $extension)
    {
        $extension->delete();

        return redirect()->route('admin.extensions.index')
            ->with('success', 'Pengajuan perpanjangan berhasil dihapus.');
    }
}