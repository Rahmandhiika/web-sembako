<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PembayaranUtang extends Model
{
    protected $fillable = ['utang_id', 'nominal', 'tanggal'];
    protected $casts = ['tanggal' => 'date'];

    public function utang()
    {
        return $this->belongsTo(Utang::class);
    }
}
