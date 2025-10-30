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
        Schema::create('lot_penggunaan_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('produksi_id')->nullable()->constrained();
            $table->foreignId('lot_bahan_baku_id')->constrained();
            $table->decimal('jumlah', 15, 3);
            $table->dateTime('tanggal_penggunaan');
            $table->foreignId('user_id')->constrained();
            // $table->enum('action', ['produksi', 'returned']); // e.g 'produksi', 'returned'
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('lot_penggunaan_logs');
    }
};
