<?php

namespace Database\Seeders;

use App\Models\Maintenance;
use App\Models\Tenant;
use Illuminate\Database\Seeder;

class MaintenanceSeeder extends Seeder
{
    public function run(): void
    {
        if (Maintenance::exists()) {
            return;
        }

        $tenants = Tenant::orderBy('id')->get();

        $reports = [
            [
                'tenant' => $tenants[0],
                'title' => 'AC tidak dingin',
                'description' => 'AC di kamar sudah 3 hari hanya mengeluarkan angin, tidak dingin sama sekali.',
                'status' => 'reported', 'priority' => 'high',
                'resolved_at' => null, 'admin_notes' => null,
            ],
            [
                'tenant' => $tenants[2],
                'title' => 'Keran kamar mandi bocor',
                'description' => 'Keran wastafel menetes terus walaupun sudah ditutup rapat.',
                'status' => 'in_progress', 'priority' => 'medium',
                'resolved_at' => null, 'admin_notes' => 'Tukang dijadwalkan datang besok pagi.',
            ],
            [
                'tenant' => $tenants[4],
                'title' => 'Lampu kamar mati',
                'description' => 'Lampu utama kamar mati, mohon diganti.',
                'status' => 'resolved', 'priority' => 'low',
                'resolved_at' => now()->subDays(2), 'admin_notes' => 'Lampu sudah diganti LED baru.',
            ],
        ];

        foreach ($reports as $r) {
            Maintenance::create([
                'tenant_id'   => $r['tenant']->id,
                'room_id'     => $r['tenant']->room_id, // kamar diambil dari data penghuni
                'title'       => $r['title'],
                'description' => $r['description'],
                'photo'       => null,
                'status'      => $r['status'],
                'priority'    => $r['priority'],
                'resolved_at' => $r['resolved_at'],
                'admin_notes' => $r['admin_notes'],
            ]);
        }
    }
}
