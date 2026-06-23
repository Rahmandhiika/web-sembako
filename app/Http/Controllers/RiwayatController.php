<?php

namespace App\Http\Controllers;

use App\Models\Penjualan;
use App\Models\Toko;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class RiwayatController extends Controller
{
    public function index(Request $request)
    {
        $user = auth()->user();
        $tokos = Toko::all();
        $tokoId = $user->isKasir() ? $user->toko_id : $request->get('toko_id');

        $penjualans = Penjualan::with(['produk', 'toko'])
            ->when($tokoId, fn($q) => $q->where('toko_id', $tokoId))
            ->latest('tanggal')->latest('id')
            ->paginate(20)
            ->withQueryString();

        $isKasir = $user->isKasir();

        return view('riwayat.index', compact('penjualans', 'tokos', 'tokoId', 'isKasir'));
    }

    public function batalkan(Penjualan $penjualan)
    {
        $user = auth()->user();

        if ($user->isKasir() && $penjualan->toko_id !== $user->toko_id) {
            abort(403);
        }

        if ($penjualan->status === 'dibatalkan') {
            return back()->withErrors(['error' => 'Transaksi sudah dibatalkan sebelumnya.']);
        }

        DB::transaction(function () use ($penjualan) {
            $penjualan->update(['status' => 'dibatalkan']);
            $penjualan->produk->increment('stok', $penjualan->jumlah);
        });

        return back()->with('success', 'Transaksi berhasil dibatalkan. Stok telah dikembalikan.');
    }
}
