<?php

namespace Database\Seeders;

use App\Models\Property;
use App\Models\Room;
use Illuminate\Database\Seeder;

class RoomSeeder extends Seeder
{
    public function run(): void
    {
        $property = Property::where('name', 'Kos Seruni')->firstOrFail();

        // [nomor, tipe, harga, ukuran, fasilitas, status]
        $rooms = [
            ['A01', 'Standard', 800000,  '3x3', ['Kasur', 'Lemari', 'Kipas Angin', 'WiFi'],                         'occupied'],
            ['A02', 'Standard', 850000,  '3x3', ['Kasur', 'Lemari', 'Kipas Angin', 'WiFi'],                         'occupied'],
            ['A03', 'Standard', 900000,  '3x4', ['Kasur', 'Lemari', 'Meja Belajar', 'Kipas Angin', 'WiFi'],        'occupied'],
            ['A04', 'Standard', 1000000, '3x4', ['Kasur', 'Lemari', 'Meja Belajar', 'AC', 'WiFi'],                 'occupied'],
            ['A05', 'Deluxe',   1200000, '4x4', ['Kasur', 'Lemari', 'Meja Belajar', 'AC', 'WiFi', 'KM Dalam'],     'occupied'],
            ['A06', 'Deluxe',   1250000, '4x4', ['Kasur', 'Lemari', 'Meja Belajar', 'AC', 'WiFi', 'KM Dalam'],     'occupied'],
            ['A07', 'Deluxe',   1350000, '4x5', ['Kasur', 'Lemari', 'Meja Kerja', 'AC', 'WiFi', 'KM Dalam', 'Water Heater'], 'occupied'],
            ['A08', 'Deluxe',   1400000, '4x5', ['Kasur', 'Lemari', 'Meja Kerja', 'AC', 'WiFi', 'KM Dalam', 'Water Heater'], 'occupied'],
            ['A09', 'Standard', 950000,  '3x4', ['Kasur', 'Lemari', 'Meja Belajar', 'AC', 'WiFi'],                 'available'],
            ['A10', 'Deluxe',   1500000, '5x5', ['Kasur King', 'Lemari', 'Meja Kerja', 'AC', 'WiFi', 'KM Dalam', 'Water Heater', 'TV'], 'available'],
        ];

        foreach ($rooms as [$number, $type, $price, $size, $facilities, $status]) {
            Room::updateOrCreate(
                ['property_id' => $property->id, 'room_number' => $number],
                [
                    'type'        => $type,
                    'price'       => $price,
                    'size'        => $size,
                    'facilities'  => $facilities, // di-cast otomatis ke JSON string
                    'status'      => $status,
                    'description' => "Kamar {$type} ukuran {$size} meter.",
                    'photo'       => null,
                ]
            );
        }
    }
}
