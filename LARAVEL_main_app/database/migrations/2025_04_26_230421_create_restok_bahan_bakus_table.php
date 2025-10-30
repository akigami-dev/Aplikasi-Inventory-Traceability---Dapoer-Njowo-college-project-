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
        Schema::create('restok_bahan_bakus', function (Blueprint $table) {
            $table->id();
            $table->string('kode_restok')->unique();
            $table->foreignId('master_bahan_baku_id')->constrained();
            $table->decimal('jumlah_restok', 15, 3);
            $table->date('tanggal_restok');
            $table->boolean('is_archived')->default(false);
            $table->enum('status', ['pending', 'used', 'archived'])->default('pending');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('restok_bahan_bakus');
    }
};
