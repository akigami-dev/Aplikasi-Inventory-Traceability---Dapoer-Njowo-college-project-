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
        Schema::create('produksis', function (Blueprint $table) {
            $table->id();
            $table->string('nomor_batch')->unique()->nullable();
            $table->foreignId('master_produk_id')->constrained();
            $table->integer('kuantitas');
            $table->date('tanggal_kadaluarsa')->nullable();
            $table->enum('status', ['Pending', 'Proses', 'Pengemasan', 'Selesai', 'Batal'])->default('Pending');
            $table->foreignId('lokasi_penyimpanan_id')->constrained('lokasi_penyimpanans');
            $table->decimal('suhu_penyimpanan', 6, 2)->default(0.0); // Default suhu 0.0°C
            $table->dateTime('mulai_produksi')->nullable();
            $table->dateTime('selesai_produksi')->nullable();
            $table->string('qrcode_path')->nullable();
            $table->string('label_path')->nullable();
            $table->foreignId('user_id')->constrained();
            $table->boolean('is_archived')->default(false);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('produksis');
    }
};
