<?php

namespace App\Http\Controllers;

use App\Models\Penjualan;
use App\Models\Piutang;
use App\Models\Produk;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PenjualanController extends Controller
{
    public function index(Request $request)
    {
        $user = auth()->user();

        $produks = Produk::with('kategori')
            ->where('toko_id', $user->toko_id)
            ->where('stok', '>', 0)
            ->orderBy('nama')
            ->get();

        $startDate = $request->get('start_date', date('Y-m-d'));
        $endDate = $request->get('end_date', date('Y-m-d'));

        $penjualanHariIni = Penjualan::with('produk')
            ->where('toko_id', $user->toko_id)
            ->whereBetween('tanggal', [$startDate, $endDate])
            ->latest('id')
            ->get();

        return view('kasir.penjualan', compact('produks', 'penjualanHariIni'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'produk_id' => 'required|exists:produks,id',
            'jumlah' => 'required|integer|min:1',
            'harga_jual' => 'required|numeric|min:0',
            'tanggal' => 'required|date',
            'nama_kasir' => 'required|string|max:255',
            'catatan' => 'nullable|string',
            'is_piutang' => 'nullable|boolean',
            'piutang_nama' => 'required_if:is_piutang,1|nullable|string|max:255',
            'piutang_jatuh_tempo' => 'required_if:is_piutang,1|nullable|date',
            'piutang_keterangan' => 'nullable|string',
        ]);

        $produk = Produk::findOrFail($request->produk_id);
        $user = auth()->user();

        if ($produk->toko_id !== $user->toko_id) {
            abort(403);
        }

        if ($produk->stok < $request->jumlah) {
            return back()->withErrors(['jumlah' => 'Stok tidak mencukupi. Stok tersedia: ' . $produk->stok])->withInput();
        }

        DB::transaction(function () use ($request, $produk, $user) {
            $penjualan = Penjualan::create([
                'produk_id' => $produk->id,
                'toko_id' => $user->toko_id,
                'jumlah' => $request->jumlah,
                'harga_jual' => $request->harga_jual,
                'harga_beli_saat_itu' => $produk->harga_beli,
                'tanggal' => $request->tanggal,
                'catatan' => $request->catatan,
                'nama_kasir' => $request->nama_kasir,
            ]);
            $produk->decrement('stok', $request->jumlah);

            if ($request->is_piutang) {
                $nominal = $request->harga_jual * $request->jumlah;
                Piutang::create([
                    'toko_id' => $user->toko_id,
                    'nama_pelanggan' => $request->piutang_nama,
                    'nominal' => $nominal,
                    'sisa' => $nominal,
                    'tanggal' => $request->tanggal,
                    'jatuh_tempo' => $request->piutang_jatuh_tempo,
                    'keterangan' => $request->piutang_keterangan ?? $produk->nama . ' x' . $request->jumlah,
                ]);
            }
        });

        $msg = 'Penjualan berhasil dicatat.';
        if ($request->is_piutang) {
            $msg .= ' Piutang untuk ' . $request->piutang_nama . ' juga tercatat.';
        }

        return redirect()->route('penjualan.index')->with('success', $msg);
    }

    public function getHarga(Produk $produk)
    {
        return response()->json([
            'harga_jual' => $produk->harga_jual,
            'stok' => $produk->stok,
        ]);
    }
}
