@extends('layouts.app')
@section('title', 'Inventory - Master Jaya')
@section('sidebar') @include('layouts.sidebar-admin') @endsection
@section('mobile-nav') @include('layouts.sidebar-admin') @endsection

@section('content')
<h4 class="fw-bold mb-4"><i class="bi bi-box-seam me-2"></i>Inventory</h4>

{{-- Filter --}}
<div class="card mb-4">
    <div class="card-body">
        <form method="GET" class="row g-3 align-items-end">
            <div class="col-md-3">
                <label class="form-label fw-semibold">Toko</label>
                <select name="toko_id" class="form-select" onchange="this.form.submit()">
                    <option value="">Semua Toko</option>
                    @foreach($tokos as $toko)
                        <option value="{{ $toko->id }}" {{ $tokoId == $toko->id ? 'selected' : '' }}>{{ $toko->nama }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-3">
                <label class="form-label fw-semibold">Kategori</label>
                <select name="kategori_id" class="form-select" onchange="this.form.submit()">
                    <option value="">Semua Kategori</option>
                    @foreach($kategoris as $kat)
                        <option value="{{ $kat->id }}" {{ request('kategori_id') == $kat->id ? 'selected' : '' }}>{{ $kat->nama }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-4">
                <label class="form-label fw-semibold">Cari Produk</label>
                <div class="input-group">
                    <input type="text" name="search" class="form-control" placeholder="Nama produk..." value="{{ request('search') }}">
                    <button class="btn btn-mj"><i class="bi bi-search"></i></button>
                </div>
            </div>
        </form>
    </div>
</div>

{{-- Ringkasan --}}
<div class="row g-3 mb-4">
    <div class="col-md-4 col-6">
        <div class="card card-summary p-3">
            <div class="card-title">Total Produk</div>
            <div class="card-value">{{ $totalProduk }}</div>
        </div>
    </div>
    <div class="col-md-4 col-6">
        <div class="card card-summary p-3">
            <div class="card-title">Total Nilai Stok</div>
            <div class="card-value">Rp {{ number_format($totalNilaiStok, 0, ',', '.') }}</div>
        </div>
    </div>
    <div class="col-md-4 col-6">
        <div class="card card-summary p-3" style="border-left-color: var(--mj-red);">
            <div class="card-title">Stok Menipis / Habis</div>
            <div class="card-value text-danger">{{ $stokMenipisCount }}</div>
        </div>
    </div>
</div>

{{-- Daftar Produk --}}
<div class="card">
    <div class="card-header fw-bold">Daftar Stok Produk</div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead>
                    <tr>
                        <th>Produk</th>
                        <th>Toko</th>
                        <th>Kategori</th>
                        <th>Stok</th>
                        <th>Min</th>
                        <th>Nilai Stok</th>
                        <th>Status</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($produks as $produk)
                    <tr>
                        <td class="fw-semibold">{{ $produk->nama }}</td>
                        <td>{{ $produk->toko->nama }}</td>
                        <td>{{ $produk->kategori->nama }}</td>
                        <td>{{ $produk->stok }}</td>
                        <td>{{ $produk->stok_minimum }}</td>
                        <td class="fw-semibold">{{ $produk->harga_beli ? 'Rp ' . number_format($produk->stok * $produk->harga_beli, 0, ',', '.') : '-' }}</td>
                        <td>
                            <span class="badge {{ $produk->status_stok == 'Habis' ? 'badge-habis' : ($produk->status_stok == 'Menipis' ? 'badge-menipis' : 'badge-normal') }}">
                                {{ $produk->status_stok }}
                            </span>
                        </td>
                        <td>
                            <button class="btn btn-sm btn-mj" data-bs-toggle="modal" data-bs-target="#editStokModal{{ $produk->id }}">
                                <i class="bi bi-pencil me-1"></i>Edit
                            </button>
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="8" class="text-center text-muted py-4">Belum ada produk.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
<div class="mt-3">{{ $produks->links() }}</div>

{{-- Edit Stok Modals --}}
@foreach($produks as $produk)
<div class="modal fade" id="editStokModal{{ $produk->id }}" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form method="POST" action="{{ route('inventory.updateStok', $produk) }}">
                @csrf @method('PUT')
                <div class="modal-header">
                    <h5 class="modal-title"><i class="bi bi-pencil me-2"></i>Edit Stok: {{ $produk->nama }}</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <p class="text-muted mb-3">{{ $produk->toko->nama }} — {{ $produk->kategori->nama }}</p>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Stok Saat Ini <span class="text-danger">*</span></label>
                        <input type="number" name="stok" class="form-control form-control-lg" value="{{ $produk->stok }}" required min="0">
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Stok Minimum</label>
                        <input type="number" name="stok_minimum" class="form-control form-control-lg" value="{{ $produk->stok_minimum }}" required min="0">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-gold"><i class="bi bi-check-lg me-1"></i>Simpan</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endforeach
@endsection
