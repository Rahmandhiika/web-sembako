<?php

namespace App\Http\Controllers;

use App\Models\Kategori;
use App\Models\Produk;
use App\Models\Toko;
use Illuminate\Http\Request;

class InventoryController extends Controller
{
    public function index(Request $request)
    {
        $tokos = Toko::all();
        $kategoris = Kategori::all();
        $tokoId = $request->get('toko_id');

        $produks = Produk::with(['toko', 'kategori'])
            ->when($tokoId, fn($q) => $q->where('toko_id', $tokoId))
            ->when($request->kategori_id, fn($q) => $q->where('kategori_id', $request->kategori_id))
            ->when($request->search, fn($q) => $q->where('nama', 'like', '%' . $request->search . '%'))
            ->orderBy('nama')
            ->paginate(20)
            ->withQueryString();

        $baseQuery = Produk::query()
            ->when($tokoId, fn($q) => $q->where('toko_id', $tokoId))
            ->when($request->kategori_id, fn($q) => $q->where('kategori_id', $request->kategori_id))
            ->when($request->search, fn($q) => $q->where('nama', 'like', '%' . $request->search . '%'));

        $totalProduk = (clone $baseQuery)->count();
        $totalNilaiStok = (clone $baseQuery)->whereNotNull('harga_beli')
            ->selectRaw('SUM(stok * harga_beli) as total')->value('total') ?? 0;
        $stokMenipisCount = (clone $baseQuery)->where(function ($q) {
            $q->where('stok', '<=', 0)->orWhereColumn('stok', '<=', 'stok_minimum');
        })->count();

        return view('admin.inventory.index', compact('tokos', 'kategoris', 'tokoId', 'produks', 'totalProduk', 'totalNilaiStok', 'stokMenipisCount'));
    }

    public function updateStok(Request $request, Produk $produk)
    {
        $request->validate([
            'stok' => 'required|integer|min:0',
            'stok_minimum' => 'required|integer|min:0',
        ]);

        $produk->update($request->only(['stok', 'stok_minimum']));
        return redirect()->route('inventory.index')->with('success', 'Stok "' . $produk->nama . '" berhasil diperbarui.');
    }
}
