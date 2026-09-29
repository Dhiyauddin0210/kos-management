<?php

namespace Database\Seeders;

use App\Models\Extension;
use App\Models\Tenant;
use Illuminate\Database\Seeder;

class ExtensionSeeder extends Seeder
{
    public function run(): void
    {
        if (Extension::exists()) {
            return;
        }

        // Penghuni yang paling lama (masa sewa hampir habis) mengajukan perpanjangan.
        $tenants = Tenant::with('room')->orderBy('id')->take(2)->get();

        // 1) Pengajuan masih menunggu keputusan admin
        $t1 = $tenants[0];
        Extension::create([
            'tenant_id'          => $t1->id,
            'current_end_date'   => $t1->end_date,
            'requested_end_date' => $t1->end_date->copy()->addMonths(6),
            'duration_months'    => 6,
            'additional_cost'    => $t1->room->price * 6, // biaya = harga kamar x durasi
            'status'             => 'pending',
            'notes'              => 'Saya ingin perpanjang 6 bulan lagi, masih nyaman di sini.',
            'admin_notes'        => null,
            'approved_at'        => null,
        ]);

        // 2) Pengajuan yang sudah disetujui.
        // Catatan: seeder ini tidak mengubah end_date tenant. Di aplikasi asli,
        // saat approve, end_date tenant sebaiknya diupdate ke requested_end_date.
        $t2 = $tenants[1];
        Extension::create([
            'tenant_id'          => $t2->id,
            'current_end_date'   => $t2->end_date,
            'requested_end_date' => $t2->end_date->copy()->addMonths(3),
            'duration_months'    => 3,
            'additional_cost'    => $t2->room->price * 3,
            'status'             => 'approved',
            'notes'              => 'Perpanjang 3 bulan dulu ya, Pak.',
            'admin_notes'        => 'Disetujui, harga tetap.',
            'approved_at'        => now()->subDays(1),
        ]);
    }
}
