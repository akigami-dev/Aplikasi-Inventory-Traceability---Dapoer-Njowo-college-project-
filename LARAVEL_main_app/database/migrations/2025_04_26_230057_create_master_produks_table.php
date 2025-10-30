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
        Schema::create('master_produks', function (Blueprint $table) {
            $table->id();
            $table->string('kode_produk')->unique();
            $table->string('nama_produk');
            $table->string('gambar_path');
            // $table->string('kategori');
            // $table->foreignId('produk_kategori_id')->constrained();
            $table->decimal('harga');
            $table->decimal('berat_bersih');
            $table->string('satuan_berat');
            $table->string('template_label_path');
            $table->boolean('is_archived')->default(false);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('master_produks');
    }
};
