<?php

namespace Database\Seeders;

use App\Models\Room;
use App\Models\Tenant;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Seeder;

class TenantSeeder extends Seeder
{
    public function run(): void
    {
        // Berapa bulan yang lalu masing-masing penghuni mulai ngekos (variasi 1-11 bulan).
        $monthsAgo = [11, 9, 8, 6, 5, 4, 3, 1];

        // Alamat asal (realistis, beda-beda kota)
        $addresses = [
            'Jl. Merdeka No. 12, Bandung, Jawa Barat',
            'Jl. Diponegoro No. 45, Semarang, Jawa Tengah',
            'Jl. Pemuda No. 8, Surabaya, Jawa Timur',
            'Jl. Ahmad Yani No. 101, Yogyakarta, DIY',
            'Jl. Sudirman No. 77, Medan, Sumatera Utara',
            'Jl. Gajah Mada No. 30, Denpasar, Bali',
            'Jl. Veteran No. 5, Malang, Jawa Timur',
            'Jl. Pahlawan No. 19, Palembang, Sumatera Selatan',
        ];

        for ($i = 1; $i <= 8; $i++) {
            $user = User::where('email', "penghuni{$i}@kos.test")->firstOrFail();
            $room = Room::where('room_number', sprintf('A%02d', $i))->firstOrFail();

            // Mulai sewa selalu tanggal 1 supaya periode tagihan bulanan rapi.
            $start = Carbon::today()->startOfMonth()->subMonths($monthsAgo[$i - 1]);
            $duration = 12;

            Tenant::updateOrCreate(
                ['user_id' => $user->id],
                [
                    'room_id'         => $room->id,
                    'full_name'       => $user->name,
                    // KTP 16 digit dummy: 3171 (kode wilayah) + 12 digit urutan
                    'ktp_number'      => '3171' . str_pad((string) (20000000 + $i * 137), 12, '0', STR_PAD_LEFT),
                    'phone'           => $user->phone,
                    'address'         => $addresses[$i - 1],
                    'start_date'      => $start,
                    'end_date'        => $start->copy()->addMonths($duration),
                    'duration_months' => $duration,
                    'status'          => 'active',
                ]
            );
        }
    }
}
