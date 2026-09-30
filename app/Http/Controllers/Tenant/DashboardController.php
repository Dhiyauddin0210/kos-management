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

        $currentInvoice = Invoice::where('tenant_id', $tenant->id)
            ->where('month', now()->month)
            ->where('year', now()->year)
            ->first();

        $unpaidInvoices = Invoice::where('tenant_id', $tenant->id)
            ->whereIn('status', ['unpaid', 'pending', 'overdue'])
            ->orderBy('due_date', 'asc')
            ->get();

        $totalDue = $unpaidInvoices->sum('amount');

        $daysLeft = $tenant->end_date ? (int) now()->startOfDay()->diffInDays($tenant->end_date->startOfDay(), false) : 0;

        $recentInvoices = Invoice::where('tenant_id', $tenant->id)
            ->orderBy('year', 'desc')
            ->orderBy('month', 'desc')
            ->limit(5)
            ->get();

        $recentPayments = Payment::whereHas('invoice', fn ($q) => $q->where('tenant_id', $tenant->id))
            ->with('invoice')
            ->orderBy('payment_date', 'desc')
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