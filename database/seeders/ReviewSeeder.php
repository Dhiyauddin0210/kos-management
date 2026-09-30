<?php

namespace Database\Seeders;

use App\Models\Property;
use App\Models\Review;
use App\Models\User;
use Illuminate\Database\Seeder;

class ReviewSeeder extends Seeder
{
    public function run(): void
    {
        if (Review::exists()) {
            return;
        }

        $property = Property::where('is_active', true)->first();
        $admin = User::where('role', 'admin')->first();

        // Ambil 5 penghuni
        $users = User::where('role', 'penghuni')->limit(5)->get();

        $reviews = [
            [
                'rating' => 5, 'cleanliness' => 5, 'security' => 5, 'facilities' => 5, 'price' => 4, 'friendliness' => 5,
                'comment' => 'Lokasi strategis banget, dekat kantor. Kamarnya bersih, WiFi kencang. Betah 2 tahun di sini!',
                'status' => 'approved', 'anonymous' => false,
            ],
            [
                'rating' => 5, 'cleanliness' => 4, 'security' => 5, 'facilities' => 4, 'price' => 5, 'friendliness' => 5,
                'comment' => 'Pemilik kosnya ramah, kayak keluarga sendiri. Fasilitas lengkap, harga masuk kantong mahasiswa.',
                'status' => 'approved', 'anonymous' => false,
            ],
            [
                'rating' => 4, 'cleanliness' => 4, 'security' => 5, 'facilities' => 4, 'price' => 4, 'friendliness' => 5,
                'comment' => 'AC dingin, kamar mandi dalam. Cocok buat kerja remote seharian tanpa gangguan.',
                'status' => 'approved', 'anonymous' => true,
            ],
            [
                'rating' => 5, 'cleanliness' => 5, 'security' => 5, 'facilities' => 5, 'price' => 5, 'friendliness' => 5,
                'comment' => 'Sudah 3 tahun tinggal di sini, ga pernah nyesel. Bersih, aman, nyaman pokoknya.',
                'status' => 'pending', 'anonymous' => false,
            ],
            [
                'rating' => 4, 'cleanliness' => 4, 'security' => 4, 'facilities' => 3, 'price' => 5, 'friendliness' => 4,
                'comment' => 'Harga bersaing, fasilitas oke. Cuma parkir agak sempit kalau sore.',
                'status' => 'pending', 'anonymous' => false,
            ],
        ];

        foreach ($reviews as $i => $r) {
            if (! isset($users[$i])) break;

            Review::create([
                'user_id'             => $users[$i]->id,
                'property_id'         => $property?->id,
                'rating'              => $r['rating'],
                'rating_cleanliness'  => $r['cleanliness'],
                'rating_security'     => $r['security'],
                'rating_facilities'   => $r['facilities'],
                'rating_price'        => $r['price'],
                'rating_friendliness' => $r['friendliness'],
                'comment'             => $r['comment'],
                'is_anonymous'        => $r['anonymous'],
                'status'              => $r['status'],
                'approved_at'         => $r['status'] === 'approved' ? now() : null,
                'approved_by'         => $r['status'] === 'approved' ? $admin?->id : null,
            ]);
        }
    }
}