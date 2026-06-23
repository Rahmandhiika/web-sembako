<?php

namespace App\Http\Controllers;

use App\Models\Penjualan;
use App\Models\Piutang;
use App\Models\Toko;
use App\Models\Utang;
use Illuminate\Http\Request;

class LaporanController extends Controller
{
    public function index(Request $request)
    {
        $tokos = Toko::all();
        $tokoId = $request->get('toko_id');
        $periode = $request->get('periode', 'bulan');

        [$startDate, $endDate] = $this->getPeriodeDates($periode, $request);

        $query = Penjualan::where('status', 'aktif')
            ->when($tokoId, fn($q) => $q->where('toko_id', $tokoId))
            ->whereBetween('tanggal', [$startDate, $endDate]);

        $totalTransaksi = (clone $query)->count();
        $totalOmzet = (clone $query)->selectRaw('SUM(harga_jual * jumlah) as total')->value('total') ?? 0;
        $totalModal = (clone $query)->whereNotNull('harga_beli_saat_itu')
            ->selectRaw('SUM(harga_beli_saat_itu * jumlah) as total')->value('total') ?? 0;
        $labaBersih = $totalOmzet - $totalModal;
        $marginLaba = $totalOmzet > 0 ? round(($labaBersih / $totalOmzet) * 100, 2) : 0;

        $utangAktif = Utang::where('sisa', '>', 0)->sum('sisa');
        $piutangAktif = Piutang::when($tokoId, fn($q) => $q->where('toko_id', $tokoId))
            ->where('sisa', '>', 0)->sum('sisa');

        $labaPerProduk = Penjualan::where('status', 'aktif')
            ->when($tokoId, fn($q) => $q->where('toko_id', $tokoId))
            ->whereBetween('tanggal', [$startDate, $endDate])
            ->whereNotNull('harga_beli_saat_itu')
            ->join('produks', 'penjualans.produk_id', '=', 'produks.id')
            ->join('tokos', 'penjualans.toko_id', '=', 'tokos.id')
            ->selectRaw('produks.nama as produk_nama, tokos.nama as toko_nama,
                SUM(penjualans.jumlah) as total_terjual,
                SUM(penjualans.harga_jual * penjualans.jumlah) as omzet,
                SUM(penjualans.harga_beli_saat_itu * penjualans.jumlah) as modal,
                SUM((penjualans.harga_jual - penjualans.harga_beli_saat_itu) * penjualans.jumlah) as laba')
            ->groupBy('produks.nama', 'tokos.nama')
            ->orderByDesc('laba')
            ->get();

        $detailPenjualan = Penjualan::with(['produk', 'toko'])
            ->when($tokoId, fn($q) => $q->where('toko_id', $tokoId))
            ->whereBetween('tanggal', [$startDate, $endDate])
            ->latest('tanggal')->latest('id')
            ->paginate(20)
            ->withQueryString();

        return view('admin.laporan.index', compact(
            'tokos', 'tokoId', 'periode', 'startDate', 'endDate',
            'totalTransaksi', 'totalOmzet', 'totalModal', 'labaBersih', 'marginLaba',
            'utangAktif', 'piutangAktif', 'labaPerProduk', 'detailPenjualan'
        ));
    }

    public function resetData()
    {
        \Illuminate\Support\Facades\Artisan::call('migrate:fresh', ['--seed' => true]);
        return redirect()->route('login')->with('success', 'Semua data berhasil direset. Silakan login kembali.');
    }

    private function getPeriodeDates($periode, $request)
    {
        return match ($periode) {
            'hari' => [now()->toDateString(), now()->toDateString()],
            'minggu' => [now()->startOfWeek()->toDateString(), now()->endOfWeek()->toDateString()],
            'custom' => [$request->get('start_date', now()->startOfMonth()->toDateString()), $request->get('end_date', now()->toDateString())],
            default => [now()->startOfMonth()->toDateString(), now()->endOfMonth()->toDateString()],
        };
    }
}
