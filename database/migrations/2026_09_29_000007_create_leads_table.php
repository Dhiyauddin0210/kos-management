<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('leads', function (Blueprint $table) {
            $table->id();
            // Nullable: calon penghuni bisa saja belum memilih properti/kamar tertentu.
            // nullOnDelete: data lead tetap ada walau properti/kamar dihapus.
            $table->foreignId('property_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('room_id')->nullable()->constrained()->nullOnDelete();
            $table->string('name');
            $table->string('phone', 20);
            $table->string('email')->nullable();
            $table->text('message')->nullable();
            $table->string('source', 50)->default('qr'); // qr / web / manual
            $table->enum('status', ['new', 'contacted', 'closed'])->default('new');
            $table->text('follow_up_notes')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('leads');
    }
};
