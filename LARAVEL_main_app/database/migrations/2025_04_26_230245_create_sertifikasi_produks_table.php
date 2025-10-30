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
        Schema::create('sertifikasi_produks', function (Blueprint $table) {
            $table->id();
            $table->foreignId('master_produk_id')->constrained();
            $table->foreignId('sertifikasi_id')->constrained();
            $table->timestamps();
            $table->softDeletes();
        });
    }
    
    public function down(): void
    {
        Schema::dropIfExists('sertifikasi_produks');
    }
};
