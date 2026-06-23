<?php

namespace App\Http\Controllers;

use App\Models\PembayaranPiutang;
use App\Models\Piutang;
use App\Models\Toko;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PiutangController extends Controller
{
    public function index(Request $request)
    {
        $user = auth()->user();
        $tokos = Toko::all();
        $tokoId = $user->isKasir() ? $user->toko_id : $request->get('toko_id');

        $piutangs = Piutang::with(['toko', 'pembayarans'])
            ->when($tokoId, fn($q) => $q->where('toko_id', $tokoId))
            ->latest('tanggal')->latest('id')
            ->paginate(20)
            ->withQueryString();

        $totalSisa = Piutang::when($tokoId, fn($q) => $q->where('toko_id', $tokoId))->where('sisa', '>', 0)->sum('sisa');
        $belumLunas = Piutang::when($tokoId, fn($q) => $q->where('toko_id', $tokoId))->where('sisa', '>', 0)->count();
        $sudahLunas = Piutang::when($tokoId, fn($q) => $q->where('toko_id', $tokoId))->where('sisa', '<=', 0)->count();
        $lewatJatuhTempo = Piutang::when($tokoId, fn($q) => $q->where('toko_id', $tokoId))->where('sisa', '>', 0)->where('jatuh_tempo', '<', now()->toDateString())->count();

        $isKasir = $user->isKasir();

        return view('piutang.index', compact('piutangs', 'tokos', 'tokoId', 'totalSisa', 'belumLunas', 'sudahLunas', 'lewatJatuhTempo', 'isKasir'));
    }

    public function store(Request $request)
    {
        $user = auth()->user();

        $request->validate([
            'nama_pelanggan' => 'required|string|max:255',
            'nominal' => 'required|numeric|min:1',
            'tanggal' => 'required|date',
            'jatuh_tempo' => 'required|date|after_or_equal:tanggal',
            'keterangan' => 'nullable|string',
            'toko_id' => $user->isAdmin() ? 'required|exists:tokos,id' : 'nullable',
        ]);

        $tokoId = $user->isKasir() ? $user->toko_id : $request->toko_id;

        Piutang::create([
            'toko_id' => $tokoId,
            'nama_pelanggan' => $request->nama_pelanggan,
            'nominal' => $request->nominal,
            'sisa' => $request->nominal,
            'tanggal' => $request->tanggal,
            'jatuh_tempo' => $request->jatuh_tempo,
            'keterangan' => $request->keterangan,
        ]);

        $redirect = $user->isKasir() ? 'piutang.kasir' : 'utang-piutang.index';
        $params = $user->isKasir() ? [] : ['tab' => 'piutang'];

        return redirect()->route($redirect, $params)->with('success', 'Piutang berhasil ditambahkan.');
    }

    public function bayar(Request $request, Piutang $piutang)
    {
        $user = auth()->user();
        if ($user->isKasir() && $piutang->toko_id !== $user->toko_id) {
            abort(403);
        }

        $request->validate([
            'nominal' => 'required|numeric|min:1|max:' . $piutang->sisa,
            'tanggal' => 'required|date',
        ]);

        DB::transaction(function () use ($request, $piutang) {
            PembayaranPiutang::create([
                'piutang_id' => $piutang->id,
                'nominal' => $request->nominal,
                'tanggal' => $request->tanggal,
            ]);
            $piutang->decrement('sisa', $request->nominal);
        });

        return back()->with('success', 'Pembayaran piutang berhasil dicatat.');
    }

    public function riwayatBayar(Piutang $piutang)
    {
        $pembayarans = $piutang->pembayarans()->latest('tanggal')->get();
        return response()->json($pembayarans);
    }

    public function bulkDelete(Request $request)
    {
        $request->validate(['ids' => 'required|array', 'ids.*' => 'exists:piutangs,id']);
        Piutang::whereIn('id', $request->ids)->delete();
        return back()->with('success', count($request->ids) . ' piutang berhasil dihapus.');
    }
}
