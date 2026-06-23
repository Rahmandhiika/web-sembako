<?php

namespace App\Http\Controllers;

use App\Models\Penjualan;
use App\Models\Piutang;
use App\Models\Produk;
use App\Models\Toko;
use App\Models\Utang;
use Illuminate\Http\Request;

class BerandaController extends Controller
{
    public function index(Request $request)
    {
        $tokoId = $request->get('toko_id');
        $periode = $request->get('periode', 'hari');
        $tokos = Toko::all();

        $produkQuery = Produk::query();
        $penjualanQuery = Penjualan::where('status', 'aktif');
        $piutangQuery = Piutang::query();

        if ($tokoId) {
            $produkQuery->where('toko_id', $tokoId);
            $penjualanQuery->where('toko_id', $tokoId);
            $piutangQuery->where('toko_id', $tokoId);
        }

        [$startDate, $endDate] = $this->getPeriodeDates($periode, $request);

        $penjualanPeriode = (clone $penjualanQuery)->whereBetween('tanggal', [$startDate, $endDate]);

        $totalProduk = (clone $produkQuery)->count();
        $nilaiStok = (clone $produkQuery)->whereNotNull('harga_beli')
            ->selectRaw('SUM(stok * harga_beli) as total')->value('total') ?? 0;
        $transaksiHariIni = (clone $penjualanPeriode)->count();
        $labaHariIni = (clone $penjualanPeriode)->whereNotNull('harga_beli_saat_itu')
            ->selectRaw('SUM((harga_jual - harga_beli_saat_itu) * jumlah) as total')->value('total') ?? 0;

        $bulanStart = now()->startOfMonth()->toDateString();
        $bulanEnd = now()->endOfMonth()->toDateString();
        $omzetBulan = Penjualan::where('status', 'aktif')
            ->when($tokoId, fn($q) => $q->where('toko_id', $tokoId))
            ->whereBetween('tanggal', [$bulanStart, $bulanEnd])
            ->selectRaw('SUM(harga_jual * jumlah) as total')->value('total') ?? 0;

        $utangAktif = Utang::where('sisa', '>', 0)->sum('sisa');
        $piutangAktif = (clone $piutangQuery)->where('sisa', '>', 0)->sum('sisa');

        $stokMenipis = Produk::when($tokoId, fn($q) => $q->where('toko_id', $tokoId))
            ->whereColumn('stok', '<=', 'stok_minimum')
            ->with('toko')
            ->get();

        $transaksiTerbaru = Penjualan::with(['produk', 'toko'])
            ->when($tokoId, fn($q) => $q->where('toko_id', $tokoId))
            ->where('status', 'aktif')
            ->latest('tanggal')->latest('id')
            ->limit(10)->get();

        $utangJatuhTempo = Utang::where('sisa', '>', 0)
            ->where('jatuh_tempo', '<=', now()->addDays(7))
            ->orderBy('jatuh_tempo')
            ->get();

        $piutangJatuhTempo = Piutang::where('sisa', '>', 0)
            ->when($tokoId, fn($q) => $q->where('toko_id', $tokoId))
            ->where('jatuh_tempo', '<=', now()->addDays(7))
            ->with('toko')
            ->orderBy('jatuh_tempo')
            ->get();

        return view('admin.beranda', compact(
            'tokos', 'tokoId', 'periode',
            'totalProduk', 'nilaiStok', 'transaksiHariIni', 'labaHariIni',
            'omzetBulan', 'utangAktif', 'piutangAktif',
            'stokMenipis', 'transaksiTerbaru', 'utangJatuhTempo', 'piutangJatuhTempo'
        ));
    }

    private function getPeriodeDates($periode, $request)
    {
        return match ($periode) {
            'minggu' => [now()->startOfWeek()->toDateString(), now()->endOfWeek()->toDateString()],
            'bulan' => [now()->startOfMonth()->toDateString(), now()->endOfMonth()->toDateString()],
            'custom' => [$request->get('start_date', now()->toDateString()), $request->get('end_date', now()->toDateString())],
            default => [now()->toDateString(), now()->toDateString()],
        };
    }
}
