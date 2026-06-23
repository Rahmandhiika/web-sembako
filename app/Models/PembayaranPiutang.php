<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PembayaranPiutang extends Model
{
    protected $fillable = ['piutang_id', 'nominal', 'tanggal'];
    protected $casts = ['tanggal' => 'date'];

    public function piutang()
    {
        return $this->belongsTo(Piutang::class);
    }
}
