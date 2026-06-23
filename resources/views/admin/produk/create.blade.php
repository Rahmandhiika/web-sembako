@extends('layouts.app')
@section('title', 'Tambah Produk - Master Jaya')
@section('sidebar') @include('layouts.sidebar-admin') @endsection
@section('mobile-nav') @include('layouts.sidebar-admin') @endsection

@section('content')
<div class="mb-4">
    <a href="{{ route('produk.index') }}" class="text-decoration-none"><i class="bi bi-arrow-left me-1"></i>Kembali</a>
</div>

<div class="card" style="max-width: 600px;">
    <div class="card-header fw-bold"><i class="bi bi-plus-lg me-2"></i>Tambah Produk Baru</div>
    <div class="card-body">
        <form method="POST" action="{{ route('produk.store') }}">
            @csrf
            <div class="mb-3">
                <label class="form-label fw-semibold">Toko <span class="text-danger">*</span></label>
                <select name="toko_id" class="form-select form-select-lg" required>
                    <option value="">Pilih Toko</option>
                    @foreach($tokos as $toko)
                        <option value="{{ $toko->id }}" {{ old('toko_id') == $toko->id ? 'selected' : '' }}>{{ $toko->nama }}</option>
                    @endforeach
                </select>
            </div>
            <div class="mb-3">
                <label class="form-label fw-semibold">Nama Produk <span class="text-danger">*</span></label>
                <input type="text" name="nama" class="form-control form-control-lg" value="{{ old('nama') }}" required placeholder="Contoh: Minyak Goreng 1 Liter">
            </div>
            <div class="mb-3">
                <label class="form-label fw-semibold">Kategori <span class="text-danger">*</span></label>
                <select name="kategori_id" class="form-select form-select-lg" required>
                    <option value="">Pilih Kategori</option>
                    @foreach($kategoris as $kat)
                        <option value="{{ $kat->id }}" {{ old('kategori_id') == $kat->id ? 'selected' : '' }}>{{ $kat->nama }}</option>
                    @endforeach
                </select>
            </div>
            <div class="row">
                <div class="col-md-6 mb-3">
                    <label class="form-label fw-semibold">Stok Awal <span class="text-danger">*</span></label>
                    <input type="number" name="stok" class="form-control form-control-lg" value="{{ old('stok', 0) }}" required min="0">
                </div>
                <div class="col-md-6 mb-3">
                    <label class="form-label fw-semibold">Stok Minimum <span class="text-danger">*</span></label>
                    <input type="number" name="stok_minimum" class="form-control form-control-lg" value="{{ old('stok_minimum', 5) }}" required min="0">
                </div>
            </div>
            <div class="mb-3">
                <label class="form-label fw-semibold">Harga Jual Awal (Rp) <span class="text-danger">*</span></label>
                <input type="number" name="harga_jual" class="form-control form-control-lg" value="{{ old('harga_jual') }}" required min="0" placeholder="0">
            </div>
            <button type="submit" class="btn btn-gold btn-lg w-100">
                <i class="bi bi-check-lg me-2"></i>Simpan Produk
            </button>
        </form>
    </div>
</div>
@endsection
