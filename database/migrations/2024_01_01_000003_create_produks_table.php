<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('produks', function (Blueprint $table) {
            $table->id();
            $table->foreignId('toko_id')->constrained('tokos')->cascadeOnDelete();
            $table->foreignId('kategori_id')->constrained('kategoris')->cascadeOnDelete();
            $table->string('nama');
            $table->integer('stok')->default(0);
            $table->integer('stok_minimum')->default(0);
            $table->decimal('harga_jual', 15, 2)->default(0);
            $table->decimal('harga_beli', 15, 2)->nullable();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('produks');
    }
};
