<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->enum('role', ['admin', 'penghuni'])->default('penghuni')->after('password');
            $table->string('phone', 20)->nullable()->after('role');
            $table->string('photo')->nullable()->after('phone');
        });

        // User admin yang SUDAH ADA sebelum kolom role dibuat otomatis kebagian
        // default 'penghuni'. Jadi kita promosikan manual ke 'admin'.
        DB::table('users')->where('email', 'admin@kos.test')->update(['role' => 'admin']);
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['role', 'phone', 'photo']);
        });
    }
};
