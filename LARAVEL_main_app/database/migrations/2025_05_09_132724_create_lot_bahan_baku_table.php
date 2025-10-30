<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('lot_bahan_bakus', function (Blueprint $table) {
            $table->id();
            $table->string('kode_lot')->unique();
            $table->foreignId('restok_bahan_baku_id')->constrained();
            $table->enum('status', ['tersedia', 'habis', 'expired', 'archived', 'tidak tersedia'])->default('tersedia');
            $table->date('tanggal_kadaluarsa');
            $table->decimal('jumlah', 15, 3);
            $table->string('satuan');
            $table->boolean('is_archived')->default(false);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('lot_bahan_baku');
    }
};
