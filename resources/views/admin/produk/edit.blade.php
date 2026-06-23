@extends('layouts.app')
@section('title', 'Edit Produk - Master Jaya')
@section('sidebar') @include('layouts.sidebar-admin') @endsection
@section('mobile-nav') @include('layouts.sidebar-admin') @endsection

@section('content')
<div class="mb-4">
    <a href="{{ route('produk.index') }}" class="text-decoration-none"><i class="bi bi-arrow-left me-1"></i>Kembali</a>
</div>

<div class="card" style="max-width: 600px;">
    <div class="card-header fw-bold"><i class="bi bi-pencil me-2"></i>Edit Produk</div>
    <div class="card-body">
        <form method="POST" action="{{ route('produk.update', $produk) }}">
            @csrf @method('PUT')
            <div class="mb-3">
                <label class="form-label fw-semibold">Toko <span class="text-danger">*</span></label>
                <select name="toko_id" class="form-select form-select-lg" required>
                    @foreach($tokos as $toko)
                        <option value="{{ $toko->id }}" {{ $produk->toko_id == $toko->id ? 'selected' : '' }}>{{ $toko->nama }}</option>
                    @endforeach
                </select>
            </div>
            <div class="mb-3">
                <label class="form-label fw-semibold">Nama Produk <span class="text-danger">*</span></label>
                <input type="text" name="nama" class="form-control form-control-lg" value="{{ old('nama', $produk->nama) }}" required>
            </div>
            <div class="mb-3">
                <label class="form-label fw-semibold">Kategori <span class="text-danger">*</span></label>
                <select name="kategori_id" class="form-select form-select-lg" required>
                    @foreach($kategoris as $kat)
                        <option value="{{ $kat->id }}" {{ $produk->kategori_id == $kat->id ? 'selected' : '' }}>{{ $kat->nama }}</option>
                    @endforeach
                </select>
            </div>
            <div class="mb-3">
                <label class="form-label fw-semibold">Stok Saat Ini</label>
                <input type="number" class="form-control form-control-lg" value="{{ $produk->stok }}" disabled>
                <small class="text-muted">Stok bisa diubah melalui halaman Inventory.</small>
            </div>
            <div class="mb-3">
                <label class="form-label fw-semibold">Stok Minimum <span class="text-danger">*</span></label>
                <input type="number" name="stok_minimum" class="form-control form-control-lg" value="{{ old('stok_minimum', $produk->stok_minimum) }}" required min="0">
            </div>
            <div class="mb-3">
                <label class="form-label fw-semibold">Harga Jual (Rp) <span class="text-danger">*</span></label>
                <input type="number" name="harga_jual" class="form-control form-control-lg" value="{{ old('harga_jual', $produk->harga_jual) }}" required min="0">
            </div>
            <button type="submit" class="btn btn-gold btn-lg w-100">
                <i class="bi bi-check-lg me-2"></i>Simpan Perubahan
            </button>
        </form>
    </div>
</div>
@endsection
