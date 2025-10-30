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
        Schema::create('lokasi_penyimpanans', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('deskripsi')->nullable();
            $table->decimal('suhu_default', 6, 2)->default(0.0); // Default suhu 0.0°C
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('lokasi_penyimpanans');
    }
};
