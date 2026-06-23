<?php

namespace App\Http\Controllers;

use App\Models\PembayaranUtang;
use App\Models\Toko;
use App\Models\Utang;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class UtangController extends Controller
{
    public function index(Request $request)
    {
        $tokos = Toko::all();
        $tab = $request->get('tab', 'utang');

        $utangs = Utang::with(['toko', 'pembayarans'])
            ->latest('tanggal')->latest('id')
            ->paginate(20, ['*'], 'utang_page')
            ->withQueryString();

        $totalSisa = Utang::where('sisa', '>', 0)->sum('sisa');
        $belumLunas = Utang::where('sisa', '>', 0)->count();
        $sudahLunas = Utang::where('sisa', '<=', 0)->count();
        $lewatJatuhTempo = Utang::where('sisa', '>', 0)->where('jatuh_tempo', '<', now()->toDateString())->count();

        return view('admin.utang.index', compact('utangs', 'tokos', 'tab', 'totalSisa', 'belumLunas', 'sudahLunas', 'lewatJatuhTempo'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'nama' => 'required|string|max:255',
            'supplier' => 'required|string|max:255',
            'nominal' => 'required|numeric|min:1',
            'tanggal' => 'required|date',
            'jatuh_tempo' => 'required|date|after_or_equal:tanggal',
            'keterangan' => 'nullable|string',
            'toko_id' => 'nullable|exists:tokos,id',
        ]);

        Utang::create(array_merge($request->only(['nama', 'supplier', 'nominal', 'tanggal', 'jatuh_tempo', 'keterangan', 'toko_id']), [
            'sisa' => $request->nominal,
        ]));

        return redirect()->route('utang-piutang.index', ['tab' => 'utang'])->with('success', 'Utang berhasil ditambahkan.');
    }

    public function bayar(Request $request, Utang $utang)
    {
        $request->validate([
            'nominal' => 'required|numeric|min:1|max:' . $utang->sisa,
            'tanggal' => 'required|date',
        ]);

        DB::transaction(function () use ($request, $utang) {
            PembayaranUtang::create([
                'utang_id' => $utang->id,
                'nominal' => $request->nominal,
                'tanggal' => $request->tanggal,
            ]);
            $utang->decrement('sisa', $request->nominal);
        });

        return back()->with('success', 'Pembayaran utang berhasil dicatat.');
    }

    public function riwayatBayar(Utang $utang)
    {
        $pembayarans = $utang->pembayarans()->latest('tanggal')->get();
        return response()->json($pembayarans);
    }

    public function bulkDelete(Request $request)
    {
        $request->validate(['ids' => 'required|array', 'ids.*' => 'exists:utangs,id']);
        Utang::whereIn('id', $request->ids)->delete();
        return redirect()->route('utang-piutang.index', ['tab' => 'utang'])->with('success', count($request->ids) . ' utang berhasil dihapus.');
    }
}
