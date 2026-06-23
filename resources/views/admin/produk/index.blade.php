@extends('layouts.app')
@section('title', 'Produk - Master Jaya')
@section('sidebar')
    @if($isKasir) @include('layouts.sidebar-kasir') @else @include('layouts.sidebar-admin') @endif
@endsection
@section('mobile-nav')
    @if($isKasir) @include('layouts.sidebar-kasir') @else @include('layouts.sidebar-admin') @endif
@endsection

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h4 class="fw-bold mb-0"><i class="bi bi-box-seam me-2"></i>Produk</h4>
    @if(!$isKasir)
    <div>
        <button class="btn btn-mj me-2" data-bs-toggle="collapse" data-bs-target="#kategoriSection">
            <i class="bi bi-tags me-1"></i>Kelola Kategori
        </button>
        <a href="{{ route('produk.create') }}" class="btn btn-gold">
            <i class="bi bi-plus-lg me-1"></i>Tambah Produk
        </a>
    </div>
    @endif
</div>

{{-- Kategori Management --}}
@if(!$isKasir)
<div class="collapse mb-4" id="kategoriSection">
    <div class="card">
        <div class="card-header fw-bold"><i class="bi bi-tags me-2"></i>Kelola Kategori</div>
        <div class="card-body">
            <form method="POST" action="{{ route('kategori.store') }}" class="row g-3 align-items-end mb-3">
                @csrf
                <div class="col-md-6">
                    <label class="form-label fw-semibold">Nama Kategori Baru <span class="text-danger">*</span></label>
                    <input type="text" name="nama" class="form-control" required placeholder="Contoh: Alat Kebersihan">
                </div>
                <div class="col-md-3">
                    <button type="submit" class="btn btn-gold w-100"><i class="bi bi-plus-lg me-1"></i>Tambah</button>
                </div>
            </form>
            <div class="table-responsive">
                <table class="table table-sm table-hover mb-0">
                    <thead><tr><th>Nama Kategori</th><th>Jumlah Produk</th><th>Aksi</th></tr></thead>
                    <tbody>
                        @foreach($kategoris as $kat)
                        <tr>
                            <td>{{ $kat->nama }}</td>
                            <td>{{ \App\Models\Produk::where('kategori_id', $kat->id)->count() }}</td>
                            <td>
                                <form method="POST" action="{{ route('kategori.destroy', $kat) }}" class="d-inline" onsubmit="return confirm('Yakin ingin menghapus kategori &quot;{{ $kat->nama }}&quot;? Produk dengan kategori ini juga akan terhapus.')">
                                    @csrf @method('DELETE')
                                    <button class="btn btn-sm btn-outline-danger"><i class="bi bi-trash"></i></button>
                                </form>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endif

{{-- Filter --}}
<div class="card mb-4">
    <div class="card-body">
        <form method="GET" class="row g-3 align-items-end">
            @if(!$isKasir)
            <div class="col-md-3">
                <label class="form-label fw-semibold">Toko</label>
                <select name="toko_id" class="form-select" onchange="this.form.submit()">
                    <option value="">Semua Toko</option>
                    @foreach($tokos as $toko)
                        <option value="{{ $toko->id }}" {{ $tokoId == $toko->id ? 'selected' : '' }}>{{ $toko->nama }}</option>
                    @endforeach
                </select>
            </div>
            @endif
            <div class="col-md-3">
                <label class="form-label fw-semibold">Kategori</label>
                <select name="kategori_id" class="form-select" onchange="this.form.submit()">
                    <option value="">Semua Kategori</option>
                    @foreach($kategoris as $kat)
                        <option value="{{ $kat->id }}" {{ $kategoriId == $kat->id ? 'selected' : '' }}>{{ $kat->nama }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-4">
                <label class="form-label fw-semibold">Cari Produk</label>
                <div class="input-group">
                    <input type="text" name="search" class="form-control" placeholder="Nama produk..." value="{{ $search }}">
                    <button class="btn btn-mj"><i class="bi bi-search"></i></button>
                </div>
            </div>
        </form>
    </div>
</div>

{{-- Bulk delete --}}
@if(!$isKasir)
<form id="bulkForm" method="POST" action="{{ route('produk.bulkDelete') }}">
    @csrf
    <div id="bulkActions" class="mb-3 d-none">
        <button type="button" class="btn btn-danger" onclick="confirmBulkDelete()">
            <i class="bi bi-trash me-1"></i>Hapus Terpilih (<span id="selectedCount">0</span>)
        </button>
        <button type="button" class="btn btn-secondary ms-2" onclick="cancelBulk()">Batal</button>
    </div>
@endif

<div class="card">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead>
                    <tr>
                        @if(!$isKasir)<th><input type="checkbox" id="checkAll" class="form-check-input" onchange="toggleAll()"></th>@endif
                        <th>Nama</th>
                        <th>Toko</th>
                        <th>Kategori</th>
                        <th>Stok</th>
                        <th>Min</th>
                        <th>Harga Jual</th>
                        <th>Status</th>
                        @if(!$isKasir)<th>Aksi</th>@endif
                    </tr>
                </thead>
                <tbody>
                    @forelse($produks as $produk)
                    <tr>
                        @if(!$isKasir)<td><input type="checkbox" name="ids[]" value="{{ $produk->id }}" class="form-check-input bulk-check" onchange="updateCount()"></td>@endif
                        <td class="fw-semibold">{{ $produk->nama }}</td>
                        <td>{{ $produk->toko->nama }}</td>
                        <td>{{ $produk->kategori->nama }}</td>
                        <td>{{ $produk->stok }}</td>
                        <td>{{ $produk->stok_minimum }}</td>
                        <td>Rp {{ number_format($produk->harga_jual, 0, ',', '.') }}</td>
                        <td>
                            <span class="badge {{ $produk->status_stok == 'Habis' ? 'badge-habis' : ($produk->status_stok == 'Menipis' ? 'badge-menipis' : 'badge-normal') }}">
                                {{ $produk->status_stok }}
                            </span>
                        </td>
                        @if(!$isKasir)
                        <td>
                            <a href="{{ route('produk.edit', $produk) }}" class="btn btn-sm btn-mj"><i class="bi bi-pencil"></i> Edit</a>
                        </td>
                        @endif
                    </tr>
                    @empty
                    <tr><td colspan="9" class="text-center text-muted py-4">Belum ada produk.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

<div class="mt-3">{{ $produks->links() }}</div>

@if(!$isKasir)
</form>

{{-- Confirm Modal --}}
<div class="modal fade" id="confirmModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Konfirmasi Hapus</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <p>Yakin ingin menghapus <strong id="deleteCount"></strong> produk terpilih? Tindakan ini tidak dapat dibatalkan.</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                <button type="button" class="btn btn-danger" onclick="document.getElementById('bulkForm').submit()">
                    <i class="bi bi-trash me-1"></i>Ya, Hapus
                </button>
            </div>
        </div>
    </div>
</div>
@endif
@endsection

@section('scripts')
@if(!$isKasir)
<script>
function toggleAll() {
    const checked = document.getElementById('checkAll').checked;
    document.querySelectorAll('.bulk-check').forEach(cb => cb.checked = checked);
    updateCount();
}
function updateCount() {
    const count = document.querySelectorAll('.bulk-check:checked').length;
    document.getElementById('selectedCount').textContent = count;
    document.getElementById('bulkActions').classList.toggle('d-none', count === 0);
}
function cancelBulk() {
    document.getElementById('checkAll').checked = false;
    document.querySelectorAll('.bulk-check').forEach(cb => cb.checked = false);
    updateCount();
}
function confirmBulkDelete() {
    const count = document.querySelectorAll('.bulk-check:checked').length;
    document.getElementById('deleteCount').textContent = count;
    new bootstrap.Modal(document.getElementById('confirmModal')).show();
}
</script>
@endif
@endsection
