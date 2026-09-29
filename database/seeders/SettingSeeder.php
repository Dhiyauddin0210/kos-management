<?php

namespace Database\Seeders;

use App\Models\Setting;
use Illuminate\Database\Seeder;

class SettingSeeder extends Seeder
{
    public function run(): void
    {
        $settings = [
            'kos_name'     => 'Kos Adin',
            'kos_tagline'  => 'Hunian nyaman, aman, dan strategis di jantung Jakarta Selatan',
            'kos_address'  => 'Jl. Melati Raya No. 25, Tebet, Jakarta Selatan 12810',
            'kos_phone'    => '081234567890',
            'kos_whatsapp' => '6281234567890', // format internasional tanpa "+" (untuk link wa.me)
            'kos_email'    => 'info@kosadin.test',
            'kos_logo'     => null,            // diisi nanti lewat upload di halaman pengaturan
            'qr_base_url'  => 'http://localhost:8000/kos', // dasar URL yang dienkode ke QR code
        ];

        foreach ($settings as $key => $value) {
            Setting::set($key, $value); // upsert -> aman dijalankan berulang
        }
    }
}