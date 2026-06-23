<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Penjualan extends Model
{
    protected $fillable = ['produk_id', 'toko_id', 'jumlah', 'harga_jual', 'harga_beli_saat_itu', 'tanggal', 'catatan', 'nama_kasir', 'status'];
    protected $casts = ['tanggal' => 'date'];

    public function produk()
    {
        return $this->belongsTo(Produk::class);
    }

    public function toko()
    {
        return $this->belongsTo(Toko::class);
    }

    public function getSubtotalAttribute()
    {
        return $this->harga_jual * $this->jumlah;
    }
}
