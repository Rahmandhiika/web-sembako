<?php

namespace App\Http\Controllers;

use App\Models\ItemPembelian;
use App\Models\Kategori;
use App\Models\Produk;
use App\Models\Toko;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PembelianController extends Controller
{
    public function index(Request $request)
    {
        $tokos = Toko::all();
        $kategoris = Kategori::all();
        $tokoId = $request->get('toko_id');

        $produks = Produk::with(['toko', 'kategori'])
            ->when($tokoId, fn($q) => $q->where('toko_id', $tokoId))
            ->orderBy('nama')
            ->get();

        $sortBy = $request->get('sort', 'tanggal');
        $sortDir = $request->get('dir', 'desc');
        $allowedSorts = ['tanggal', 'jumlah', 'harga_beli', 'supplier'];

        $riwayatQuery = ItemPembelian::with(['produk.toko', 'produk.kategori'])
            ->when($tokoId, fn($q) => $q->whereHas('produk', fn($p) => $p->where('toko_id', $tokoId)));

        if ($request->start_date && $request->end_date) {
            $riwayatQuery->whereBetween('tanggal', [$request->start_date, $request->end_date]);
        }

        if (in_array($sortBy, $allowedSorts)) {
            $riwayatQuery->orderBy($sortBy, $sortDir);
        } elseif ($sortBy === 'produk') {
            $riwayatQuery->join('produks', 'pembelians.produk_id', '=', 'produks.id')
                ->orderBy('produks.nama', $sortDir)
                ->select('pembelians.*');
        } elseif ($sortBy === 'kategori') {
            $riwayatQuery->join('produks', 'pembelians.produk_id', '=', 'produks.id')
                ->join('kategoris', 'produks.kategori_id', '=', 'kategoris.id')
                ->orderBy('kategoris.nama', $sortDir)
                ->select('pembelians.*');
        } elseif ($sortBy === 'toko') {
            $riwayatQuery->join('produks', 'pembelians.produk_id', '=', 'produks.id')
                ->join('tokos', 'produks.toko_id', '=', 'tokos.id')
                ->orderBy('tokos.nama', $sortDir)
                ->select('pembelians.*');
        } else {
            $riwayatQuery->latest('tanggal')->latest('id');
        }

        $riwayats = $riwayatQuery->paginate(20)->withQueryString();

        return view('admin.pembelian.index', compact('tokos', 'kategoris', 'tokoId', 'produks', 'riwayats'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'produk_id' => 'required|exists:produks,id',
            'jumlah' => 'required|integer|min:1',
            'harga_beli' => 'required|numeric|min:0',
            'supplier' => 'required|string|max:255',
            'tanggal' => 'required|date',
        ]);

        DB::transaction(function () use ($request) {
            $produk = Produk::findOrFail($request->produk_id);

            ItemPembelian::create([
                'produk_id' => $request->produk_id,
                'jumlah' => $request->jumlah,
                'harga_beli' => $request->harga_beli,
                'harga_jual' => $produk->harga_jual,
                'tanggal' => $request->tanggal,
                'supplier' => $request->supplier,
            ]);

            $produk->increment('stok', $request->jumlah);
            $produk->update(['harga_beli' => $request->harga_beli]);
        });

        return redirect()->route('pembelian.index')->with('success', 'Pembelian berhasil dicatat.');
    }

    public function bulkDelete(Request $request)
    {
        $request->validate(['ids' => 'required|array', 'ids.*' => 'exists:pembelians,id']);
        ItemPembelian::whereIn('id', $request->ids)->delete();
        return redirect()->route('pembelian.index')->with('success', count($request->ids) . ' riwayat pembelian berhasil dihapus.');
    }
}
