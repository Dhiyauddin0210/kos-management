<?php

namespace App\Console\Commands;

use App\Models\Invoice;
use App\Models\Setting;
use App\Models\Tenant;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class GenerateMonthlyInvoices extends Command
{
    protected $signature = 'invoices:generate-monthly 
                            {--month= : Bulan target (1-12). Default: bulan depan}
                            {--year= : Tahun target. Default: tahun depan (kalau lewat Desember)}';

    protected $description = 'Generate tagihan bulanan otomatis untuk semua penghuni aktif';

    public function handle(): int
    {
        // Tentukan periode target
        $targetDate = now()->startOfMonth()->addMonth();

        $month = (int) ($this->option('month') ?: $targetDate->month);
        $year  = (int) ($this->option('year') ?: $targetDate->year);

        $this->info("Generate tagihan untuk periode: {$month}/{$year}");

        // Ambil config dari settings
        $dueDate = (int) Setting::get('default_due_date', 10);

        // Ambil semua tenant aktif + relasi kamar
        $tenants = Tenant::with('room')->where('status', 'active')->get();

        if ($tenants->isEmpty()) {
            $this->warn('Tidak ada penghuni aktif. Skip.');
            return self::SUCCESS;
        }

        $created = 0;
        $skipped = 0;
        $errors  = 0;

        DB::beginTransaction();
        try {
            foreach ($tenants as $tenant) {
                // Cek duplikat
                $exists = Invoice::where('tenant_id', $tenant->id)
                    ->where('month', $month)
                    ->where('year', $year)
                    ->exists();

                if ($exists) {
                    $skipped++;
                    continue;
                }

                // Skip kalau tenant udah ga aktif lagi (double-check)
                if (! $tenant->room) {
                    $this->warn("Tenant {$tenant->full_name} ga punya kamar. Skip.");
                    $errors++;
                    continue;
                }

                try {
                    Invoice::create([
                        'tenant_id'      => $tenant->id,
                        'invoice_number' => Invoice::generateNumber($year, $month),
                        'month'          => $month,
                        'year'           => $year,
                        'amount'         => $tenant->room->price,
                        'due_date'       => Carbon::create($year, $month, $dueDate),
                        'status'         => 'unpaid',
                    ]);
                    $created++;
                } catch (\Exception $e) {
                    $this->error("Gagal bikin invoice untuk {$tenant->full_name}: {$e->getMessage()}");
                    $errors++;
                }
            }

            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            $this->error("Fatal error: {$e->getMessage()}");
            return self::FAILURE;
        }

        // Ringkasan
        $this->newLine();
        $this->info("✅ Selesai!");
        $this->table(
            ['Status', 'Jumlah'],
            [
                ['Dibuat', $created],
                ['Dilewati (sudah ada)', $skipped],
                ['Error', $errors],
                ['Total tenant aktif', $tenants->count()],
            ]
        );

        return self::SUCCESS;
    }
}