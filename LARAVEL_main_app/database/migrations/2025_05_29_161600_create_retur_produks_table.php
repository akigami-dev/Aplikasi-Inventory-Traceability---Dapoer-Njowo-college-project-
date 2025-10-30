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
        Schema::create('retur_produks', function (Blueprint $table) {
            $table->id();
            $table->foreignId('distribusi_id')->constrained();
            $table->dateTime('tanggal_retur');
            $table->integer('jumlah_retur');
            $table->text('keterangan');
            $table->foreignId('user_id')->constrained();
            $table->enum('tindakan', ['disimpan', 'dibuang', 'diperbaiki', 'diganti']);
            $table->text('catatan_tambahan')->nullable();
            $table->boolean('is_archived')->default(false);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('retur_produks');
    }
};
