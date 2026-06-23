<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Piutang extends Model
{
    protected $fillable = ['toko_id', 'nama_pelanggan', 'nominal', 'sisa', 'tanggal', 'jatuh_tempo', 'keterangan'];
    protected $casts = ['tanggal' => 'date', 'jatuh_tempo' => 'date'];

    public function toko()
    {
        return $this->belongsTo(Toko::class);
    }

    public function pembayarans()
    {
        return $this->hasMany(PembayaranPiutang::class);
    }

    public function getStatusAttribute()
    {
        if ($this->sisa <= 0) return 'Lunas';
        if ($this->jatuh_tempo < now()->startOfDay()) return 'Lewat Jatuh Tempo';
        return 'Belum Lunas';
    }
}
