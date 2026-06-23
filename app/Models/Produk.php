<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Produk extends Model
{
    protected $fillable = ['toko_id', 'kategori_id', 'nama', 'stok', 'stok_minimum', 'harga_jual', 'harga_beli'];

    public function toko()
    {
        return $this->belongsTo(Toko::class);
    }

    public function kategori()
    {
        return $this->belongsTo(Kategori::class);
    }

    public function pembelians()
    {
        return $this->hasMany(ItemPembelian::class);
    }

    public function getStatusStokAttribute()
    {
        if ($this->stok <= 0) return 'Habis';
        if ($this->stok <= $this->stok_minimum) return 'Menipis';
        return 'Normal';
    }
}
