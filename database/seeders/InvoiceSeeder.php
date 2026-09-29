<?php

namespace Database\Seeders;

use App\Models\Invoice;
use App\Models\Tenant;
use Carbon\Carbon;
use Illuminate\Database\Seeder;

class InvoiceSeeder extends Seeder
{
    public function run(): void
    {
        // Guard: hindari data ganda kalau seeder dijalankan ulang tanpa migrate:fresh.
        if (Invoice::exists()) {
            return;
        }

        $today = Carbon::today();

        // Status tagihan BULAN INI untuk tenant ke-1 s/d ke-8:
        // 4 sudah lunas, 2 menunggu verifikasi, 1 terlambat, 1 belum bayar.
        $thisMonthStatuses = ['paid', 'paid', 'paid', 'paid', 'pending', 'pending', 'overdue', 'unpaid'];

        $tenants = Tenant::with('room')->where('status', 'active')->orderBy('id')->get();

        foreach ($tenants as $i => $tenant) {
            // Bulan lalu: semua sudah lunas
            $this->createInvoice($tenant, $today->copy()->subMonthNoOverflow(), 'paid');

            // Bulan ini: campuran
            $this->createInvoice($tenant, $today->copy(), $thisMonthStatuses[$i] ?? 'unpaid');

            // Bulan depan: belum dibayar
            $this->createInvoice($tenant, $today->copy()->addMonthNoOverflow(), 'unpaid');
        }
    }

    private function createInvoice(Tenant $tenant, Carbon $period, string $status): void
    {
        $today = Carbon::today();

        // Jatuh tempo default: tanggal 10 di bulan periode tagihan
        $dueDate = Carbon::create($period->year, $period->month, 10);

        // Logika khusus supaya data konsisten dengan status:
        // - 'overdue' harus sudah lewat jatuh tempo
        // - 'unpaid'  belum boleh lewat jatuh tempo
        if ($status === 'overdue' && $dueDate->gte($today)) {
            $dueDate = $today->copy()->subDays(3);
        }
        if ($status === 'unpaid' && $dueDate->lt($today)) {
            $dueDate = $today->copy()->addDays(5);
        }

        // Waktu bayar hanya terisi kalau status 'paid'
        $paidAt = null;
        if ($status === 'paid') {
            $paidAt = $dueDate->copy()->subDays(mt_rand(1, 5))->setTime(mt_rand(8, 20), mt_rand(0, 59));
            if ($paidAt->isFuture()) {
                $paidAt = now()->subHours(2); // jangan sampai paid_at di masa depan
            }
        }

        Invoice::create([
            'tenant_id'      => $tenant->id,
            'invoice_number' => Invoice::generateNumber($period->year, $period->month),
            'month'          => $period->month,
            'year'           => $period->year,
            'amount'         => $tenant->room->price, // tagihan = harga sewa kamar
            'due_date'       => $dueDate,
            'status'         => $status,
            'paid_at'        => $paidAt,
        ]);
    }
}
