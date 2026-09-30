<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('reviews', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('property_id')->nullable()->constrained()->nullOnDelete();

            // Rating utama (1-5)
            $table->unsignedTinyInteger('rating');

            // Kategori rating (nullable, opsional)
            $table->unsignedTinyInteger('rating_cleanliness')->nullable(); // kebersihan
            $table->unsignedTinyInteger('rating_security')->nullable();    // keamanan
            $table->unsignedTinyInteger('rating_facilities')->nullable();  // fasilitas
            $table->unsignedTinyInteger('rating_price')->nullable();       // harga
            $table->unsignedTinyInteger('rating_friendliness')->nullable();// keramahan

            // Konten
            $table->text('comment');
            $table->boolean('is_anonymous')->default(false);

            // Moderasi
            $table->enum('status', ['pending', 'approved', 'rejected'])->default('pending');
            $table->text('admin_notes')->nullable();
            $table->timestamp('approved_at')->nullable();
            $table->foreignId('approved_by')->nullable()->constrained('users')->nullOnDelete();

            $table->timestamps();

            // 1 user cuma boleh 1 ulasan
            $table->unique('user_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('reviews');
    }
};