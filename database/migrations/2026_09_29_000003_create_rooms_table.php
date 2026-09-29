<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('rooms', function (Blueprint $table) {
            $table->id();
            $table->foreignId('property_id')->constrained()->cascadeOnDelete();
            $table->string('room_number', 20);
            $table->string('type', 50);                 // Standard / Deluxe
            $table->decimal('price', 12, 2);            // harga sewa per bulan
            $table->string('size', 20)->nullable();     // contoh: "3x4"
            $table->text('facilities')->nullable();     // JSON string: ["AC","WiFi","KM Dalam"]
            $table->enum('status', ['available', 'occupied', 'maintenance'])->default('available');
            $table->text('description')->nullable();
            $table->string('photo')->nullable();
            $table->timestamps();

            // Nomor kamar tidak boleh kembar dalam 1 properti
            $table->unique(['property_id', 'room_number']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('rooms');
    }
};
