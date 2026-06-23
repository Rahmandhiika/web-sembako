<?php

namespace App\Http\Controllers;

use App\Models\Kategori;
use Illuminate\Http\Request;

class KategoriController extends Controller
{
    public function store(Request $request)
    {
        $request->validate([
            'nama' => 'required|string|max:255|unique:kategoris,nama',
        ]);

        Kategori::create($request->only('nama'));
        return redirect()->route('produk.index')->with('success', 'Kategori "' . $request->nama . '" berhasil ditambahkan.');
    }

    public function destroy(Kategori $kategori)
    {
        $nama = $kategori->nama;
        $kategori->delete();
        return redirect()->route('produk.index')->with('success', 'Kategori "' . $nama . '" berhasil dihapus.');
    }
}
