<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Invoice;
use App\Models\Lead;
use App\Models\Payment;
use App\Models\Room;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        // ---- Kartu statistik ----
        $totalRooms     = Room::count();
        $occupiedRooms  = Room::where('status', 'occupied')->count();
        $availableRooms = Room::where('status', 'available')->count();

        // Pendapatan = pembayaran yang SUDAH diverifikasi
        $revenueThisMonth = $this->revenueOf(now());

        // ---- Data chart: pendapatan 6 bulan terakhir ----
        $chartLabels = [];
        $chartData   = [];
        for ($i = 5; $i >= 0; $i--) {
            $month         = now()->startOfMonth()->subMonths($i);
            $chartLabels[] = $month->format('M Y');
            $chartData[]   = (float) $this->revenueOf($month);
        }

        // ---- Top 5 kamar (query efisien, 1x query bukan N+1) ----
        $topRooms = DB::table('rooms')
            ->leftJoin('tenants', 'tenants.room_id', '=', 'rooms.id')
            ->leftJoin('invoices', 'invoices.tenant_id', '=', 'tenants.id')
            ->leftJoin('payments', function ($join) {
                $join->on('payments.invoice_id', '=', 'invoices.id')
                     ->where('payments.status', '=', 'verified');
            })
            ->select('rooms.id', 'rooms.room_number')
            ->selectRaw('COALESCE(SUM(payments.amount), 0) as total_revenue')
            ->groupBy('rooms.id', 'rooms.room_number')
            ->orderByDesc('total_revenue')
            ->limit(5)
            ->get();

        // ---- Tabel ringkas ----
        $latestInvoices  = Invoice::with('tenant.room')->latest()->take(5)->get();
        $pendingPayments = Payment::with('invoice.tenant')->where('status', 'pending')->latest()->take(5)->get();
        $latestLeads     = Lead::with('room')->latest()->take(5)->get();

        return view('admin.dashboard', compact(
            'totalRooms', 'occupiedRooms', 'availableRooms', 'revenueThisMonth',
            'chartLabels', 'chartData', 'topRooms',
            'latestInvoices', 'pendingPayments', 'latestLeads'
        ));
    }

    private function revenueOf($date): float
    {
        return (float) Payment::where('status', 'verified')
            ->whereYear('payment_date', $date->year)
            ->whereMonth('payment_date', $date->month)
            ->sum('amount');
    }
}