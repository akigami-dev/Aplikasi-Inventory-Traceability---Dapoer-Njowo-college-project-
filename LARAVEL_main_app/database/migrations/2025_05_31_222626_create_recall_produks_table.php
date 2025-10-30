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
        Schema::create('recall_produks', function (Blueprint $table) {
            $table->id();
            $table->string('kode_recall')->unique();
            $table->foreignId('distribusi_id')->constrained();
            $table->dateTime('tanggal_recall');
            $table->integer('jumlah_recall');
            $table->string('alasan_recall');
            $table->text('keterangan')->nullable();
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
        Schema::dropIfExists('recall_produks');
    }
};
