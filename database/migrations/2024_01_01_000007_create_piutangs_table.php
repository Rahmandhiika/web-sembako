<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('piutangs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('toko_id')->constrained('tokos')->cascadeOnDelete();
            $table->string('nama_pelanggan');
            $table->decimal('nominal', 15, 2);
            $table->decimal('sisa', 15, 2);
            $table->date('tanggal');
            $table->date('jatuh_tempo');
            $table->text('keterangan')->nullable();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('piutangs');
    }
};
