<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ItemPembelian extends Model
{
    protected $table = 'pembelians';
    protected $fillable = ['produk_id', 'jumlah', 'harga_beli', 'harga_jual', 'tanggal', 'supplier'];
    protected $casts = ['tanggal' => 'date'];

    public function produk()
    {
        return $this->belongsTo(Produk::class);
    }
}
