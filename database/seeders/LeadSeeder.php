<?php

namespace Database\Seeders;

use App\Models\Lead;
use App\Models\Property;
use App\Models\Room;
use Illuminate\Database\Seeder;

class LeadSeeder extends Seeder
{
    public function run(): void
    {
        if (Lead::exists()) {
            return;
        }

        $property = Property::where('name', 'Kos Adin')->firstOrFail();
        $a09 = Room::where('room_number', 'A09')->first();
        $a10 = Room::where('room_number', 'A10')->first();

        $leads = [
            [
                'room_id' => $a09?->id, 'name' => 'Hendra Wijaya', 'phone' => '081311110001',
                'email' => 'hendra.wijaya@example.com', 'message' => 'Halo, kamar A09 masih kosong? Boleh survei hari Sabtu?',
                'source' => 'qr', 'status' => 'new', 'follow_up_notes' => null,
            ],
            [
                'room_id' => $a10?->id, 'name' => 'Ayu Permatasari', 'phone' => '081311110002',
                'email' => null, 'message' => 'Tertarik kamar Deluxe, ada diskon kalau sewa 12 bulan?',
                'source' => 'qr', 'status' => 'new', 'follow_up_notes' => null,
            ],
            [
                'room_id' => null, 'name' => 'Dimas Aditya', 'phone' => '081311110003',
                'email' => 'dimas.aditya@example.com', 'message' => 'Cari kos khusus karyawan, budget 1 juta-an.',
                'source' => 'web', 'status' => 'contacted', 'follow_up_notes' => 'Sudah dihubungi via WA, akan survei minggu depan.',
            ],
            [
                'room_id' => $a09?->id, 'name' => 'Nurul Hidayah', 'phone' => '081311110004',
                'email' => null, 'message' => 'Apakah boleh membawa kendaraan roda empat?',
                'source' => 'manual', 'status' => 'contacted', 'follow_up_notes' => 'Dijelaskan parkir hanya motor. Masih mempertimbangkan.',
            ],
            [
                'room_id' => null, 'name' => 'Yoga Pratama', 'phone' => '081311110005',
                'email' => 'yoga.pratama@example.com', 'message' => 'Mau tanya harga dan fasilitas kamar.',
                'source' => 'qr', 'status' => 'closed', 'follow_up_notes' => 'Sudah dapat kos lain, ditutup.',
            ],
        ];

        foreach ($leads as $lead) {
            Lead::create($lead + ['property_id' => $property->id]);
        }
    }
}