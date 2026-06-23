<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('penjualans', function (Blueprint $table) {
            $table->id();
            $table->foreignId('produk_id')->constrained('produks')->cascadeOnDelete();
            $table->foreignId('toko_id')->constrained('tokos')->cascadeOnDelete();
            $table->integer('jumlah');
            $table->decimal('harga_jual', 15, 2);
            $table->decimal('harga_beli_saat_itu', 15, 2)->nullable();
            $table->date('tanggal');
            $table->text('catatan')->nullable();
            $table->string('nama_kasir');
            $table->enum('status', ['aktif', 'dibatalkan'])->default('aktif');
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('penjualans');
    }
};
