<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('pembayaran_utangs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('utang_id')->constrained('utangs')->cascadeOnDelete();
            $table->decimal('nominal', 15, 2);
            $table->date('tanggal');
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('pembayaran_utangs');
    }
};
