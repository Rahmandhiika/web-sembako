<?php

namespace App\Http\Controllers;

use App\Models\Kategori;
use App\Models\Produk;
use App\Models\Toko;
use Illuminate\Http\Request;

class ProdukController extends Controller
{
    public function index(Request $request)
    {
        $tokos = Toko::all();
        $kategoris = Kategori::all();
        $tokoId = $request->get('toko_id');
        $kategoriId = $request->get('kategori_id');
        $search = $request->get('search');

        $produks = Produk::with(['toko', 'kategori'])
            ->when($tokoId, fn($q) => $q->where('toko_id', $tokoId))
            ->when($kategoriId, fn($q) => $q->where('kategori_id', $kategoriId))
            ->when($search, fn($q) => $q->where('nama', 'like', "%{$search}%"))
            ->orderBy('nama')
            ->paginate(20)
            ->withQueryString();

        $isKasir = auth()->user()->isKasir();
        if ($isKasir) {
            $tokoId = auth()->user()->toko_id;
            $produks = Produk::with(['toko', 'kategori'])
                ->where('toko_id', $tokoId)
                ->when($kategoriId, fn($q) => $q->where('kategori_id', $kategoriId))
                ->when($search, fn($q) => $q->where('nama', 'like', "%{$search}%"))
                ->orderBy('nama')
                ->paginate(20)
                ->withQueryString();
        }

        return view('admin.produk.index', compact('produks', 'tokos', 'kategoris', 'tokoId', 'kategoriId', 'search', 'isKasir'));
    }

    public function create()
    {
        $tokos = Toko::all();
        $kategoris = Kategori::all();
        return view('admin.produk.create', compact('tokos', 'kategoris'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'toko_id' => 'required|exists:tokos,id',
            'kategori_id' => 'required|exists:kategoris,id',
            'nama' => 'required|string|max:255',
            'stok' => 'required|integer|min:0',
            'stok_minimum' => 'required|integer|min:0',
            'harga_jual' => 'required|numeric|min:0',
        ]);

        Produk::create($request->only(['toko_id', 'kategori_id', 'nama', 'stok', 'stok_minimum', 'harga_jual']));
        return redirect()->route('produk.index')->with('success', 'Produk berhasil ditambahkan.');
    }

    public function edit(Produk $produk)
    {
        $tokos = Toko::all();
        $kategoris = Kategori::all();
        return view('admin.produk.edit', compact('produk', 'tokos', 'kategoris'));
    }

    public function update(Request $request, Produk $produk)
    {
        $request->validate([
            'toko_id' => 'required|exists:tokos,id',
            'kategori_id' => 'required|exists:kategoris,id',
            'nama' => 'required|string|max:255',
            'stok_minimum' => 'required|integer|min:0',
            'harga_jual' => 'required|numeric|min:0',
        ]);

        $produk->update($request->only(['toko_id', 'kategori_id', 'nama', 'stok_minimum', 'harga_jual']));
        return redirect()->route('produk.index')->with('success', 'Produk berhasil diperbarui.');
    }

    public function bulkDelete(Request $request)
    {
        $request->validate(['ids' => 'required|array', 'ids.*' => 'exists:produks,id']);
        Produk::whereIn('id', $request->ids)->delete();
        return redirect()->route('produk.index')->with('success', count($request->ids) . ' produk berhasil dihapus.');
    }
}
