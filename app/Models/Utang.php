<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Utang extends Model
{
    protected $fillable = ['nama', 'supplier', 'nominal', 'sisa', 'tanggal', 'jatuh_tempo', 'keterangan', 'toko_id'];
    protected $casts = ['tanggal' => 'date', 'jatuh_tempo' => 'date'];

    public function toko()
    {
        return $this->belongsTo(Toko::class);
    }

    public function pembayarans()
    {
        return $this->hasMany(PembayaranUtang::class);
    }

    public function getStatusAttribute()
    {
        if ($this->sisa <= 0) return 'Lunas';
        if ($this->jatuh_tempo < now()->startOfDay()) return 'Lewat Jatuh Tempo';
        return 'Belum Lunas';
    }
}
