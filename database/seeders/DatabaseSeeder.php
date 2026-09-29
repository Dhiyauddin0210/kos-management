<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            SettingSeeder::class,
            PropertySeeder::class,
            UserSeeder::class,
            RoomSeeder::class,
            TenantSeeder::class,
            InvoiceSeeder::class,
            PaymentSeeder::class,
            LeadSeeder::class,
            MaintenanceSeeder::class,
            ExtensionSeeder::class,
            BankAccountSeeder::class,
        ]);
    }
}