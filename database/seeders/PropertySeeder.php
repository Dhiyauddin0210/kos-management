<?php

namespace Database\Seeders;

use App\Models\Property;
use Illuminate\Database\Seeder;

class PropertySeeder extends Seeder
{
    public function run(): void
    {
        Property::updateOrCreate(
            ['name' => 'Kos Adin'],
            [
                'address'     => 'Jl. Melati Raya No. 25, Tebet',
                'city'        => 'Jakarta Selatan',
                'phone'       => '081234567890',
                'description' => 'Kos Adin adalah hunian nyaman dan strategis di Tebet, Jakarta Selatan. '
                    . 'Lokasi dekat Stasiun Tebet, minimarket, dan pusat kuliner. '
                    . 'Fasilitas: dapur bersama, area parkir motor, CCTV 24 jam, WiFi, dan penjaga kos.',
                'photo'       => null,
                'is_active'   => true,
            ]
        );
    }
}