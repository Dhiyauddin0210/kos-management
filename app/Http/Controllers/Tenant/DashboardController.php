<?php

namespace App\Http\Controllers\Tenant;

use App\Http\Controllers\Controller;
use App\Models\Invoice;
use App\Models\Payment;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function index()
    {
        $user = auth()->user();
        $tenant = $user->tenant()->with(['room.property'])->firstOrFail();

        // Tagihan bulan ini
        $currentInvoice = Invoice::where('tenant_id', $tenant->id)
            ->where('month', now()->month)
            ->where('year', now()->year)
            ->first();

        // Tagihan belum lunas
        $unpaidInvoices = Invoice::where('tenant_id', $tenant->id)
            ->whereIn('status', ['unpaid', 'pending', 'overdue'])
            ->orderBy('due_date')
            ->get();

        // Total tunggakan
        $totalDue = $unpaidInvoices->sum('amount');

        // Sisa hari kontrak
        $daysLeft = $tenant->end_date ? now()->diffInDays($tenant->end_date, false) : 0;

        // Tagihan terbaru (5)
        $recentInvoices = Invoice::where('tenant_id', $tenant->id)
            ->orderByDesc('year')
            ->orderByDesc('month')
            ->limit(5)
            ->get();

        // Pembayaran terbaru (5)
        $recentPayments = Payment::whereHas('invoice', fn ($q) => $q->where('tenant_id', $tenant->id))
            ->with('invoice')
            ->orderByDesc('payment_date')
            ->limit(5)
            ->get();

        return view('tenant.dashboard.index', compact(
            'tenant',
            'currentInvoice',
            'unpaidInvoices',
            'totalDue',
            'daysLeft',
            'recentInvoices',
            'recentPayments'
        ));
    }
}