<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        $password = Hash::make('12345678');

        // ---- Admin ----
        // updateOrCreate: kalau admin@kos.test sudah ada (dari setup awal) cukup di-update.
        User::updateOrCreate(
            ['email' => 'admin@kos.test'],
            [
                'name'              => 'Admin Kos',
                'password'          => $password,
                'role'              => 'admin',
                'phone'             => '081200000001',
                'email_verified_at' => now(),
            ]
        );

        // ---- 8 Penghuni ----
        $penghuni = [
            ['Budi Santoso',     '081234560001'],
            ['Siti Rahmawati',   '081234560002'],
            ['Andi Prasetyo',    '081234560003'],
            ['Dewi Lestari',     '081234560004'],
            ['Rizky Ramadhan',   '081234560005'],
            ['Putri Anggraini',  '081234560006'],
            ['Fajar Nugroho',    '081234560007'],
            ['Maya Kusumawati',  '081234560008'],
        ];

        foreach ($penghuni as $i => [$name, $phone]) {
            $n = $i + 1;

            User::updateOrCreate(
                ['email' => "penghuni{$n}@kos.test"],
                [
                    'name'              => $name,
                    'password'          => $password,
                    'role'              => 'penghuni',
                    'phone'             => $phone,
                    'email_verified_at' => now(),
                ]
            );
        }
    }
}
