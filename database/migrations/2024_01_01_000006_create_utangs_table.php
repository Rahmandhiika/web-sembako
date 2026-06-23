<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('utangs', function (Blueprint $table) {
            $table->id();
            $table->string('nama');
            $table->string('supplier')->default('Gudang Pusat');
            $table->decimal('nominal', 15, 2);
            $table->decimal('sisa', 15, 2);
            $table->date('tanggal');
            $table->date('jatuh_tempo');
            $table->text('keterangan')->nullable();
            $table->foreignId('toko_id')->nullable()->constrained('tokos')->nullOnDelete();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('utangs');
    }
};
