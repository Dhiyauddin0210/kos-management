<?php

namespace App\Http\Controllers\Tenant;

use App\Http\Controllers\Controller;
use App\Models\Invoice;
use Illuminate\Http\Request;

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
        $tenant = auth()->user()->tenant;

        // Auto-update overdue
        Invoice::where('tenant_id', $tenant->id)
            ->where('status', 'unpaid')
            ->where('due_date', '<', now()->toDateString())
            ->update(['status' => 'overdue']);

        $query = Invoice::where('tenant_id', $tenant->id)
            ->when($request->filled('status'), fn ($q) => $q->where('status', $request->status))
            ->when($request->filled('year'), fn ($q) => $q->where('year', $request->year))
            ->orderByDesc('year')
            ->orderByDesc('month')
            ->orderByDesc('id');

        $invoices = $query->paginate(10)->withQueryString();

        // Statistik
        $stats = [
            'total_invoices' => Invoice::where('tenant_id', $tenant->id)->count(),
            'total_paid'     => Invoice::where('tenant_id', $tenant->id)->where('status', 'paid')->count(),
            'total_unpaid'   => Invoice::where('tenant_id', $tenant->id)->whereIn('status', ['unpaid', 'pending', 'overdue'])->count(),
            'total_due'      => Invoice::where('tenant_id', $tenant->id)->whereIn('status', ['unpaid', 'pending', 'overdue'])->sum('amount'),
        ];

        $statusLabels = self::STATUS_LABELS;
        $years = range(now()->year - 2, now()->year + 1);

        return view('tenant.invoices.index', compact('invoices', 'stats', 'statusLabels', 'years'));
    }

    public function show(Invoice $invoice)
    {
        // Pastikan invoice ini milik tenant yang login
        $tenant = auth()->user()->tenant;
        if ($invoice->tenant_id !== $tenant->id) {
            abort(403, 'Anda tidak berhak melihat tagihan ini.');
        }

        $invoice->load(['tenant.room.property', 'payments.verifier']);

        // Rekening bank untuk transfer
        $bankAccounts = \App\Models\BankAccount::orderByDesc('is_primary')->orderBy('bank_name', 'asc')->get();

        return view('tenant.invoices.show', compact('invoice', 'bankAccounts'));
    }
}