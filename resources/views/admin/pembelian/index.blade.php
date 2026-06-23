@extends('layouts.app')
@section('title', 'Pembelian - Master Jaya')
@section('sidebar') @include('layouts.sidebar-admin') @endsection
@section('mobile-nav') @include('layouts.sidebar-admin') @endsection

@php
    if (!function_exists('pembelianSortUrl')) {
        function pembelianSortUrl($col) {
            $dir = (request('sort') === $col && request('dir', 'desc') === 'asc') ? 'desc' : 'asc';
            return request()->fullUrlWithQuery(['sort' => $col, 'dir' => $dir, 'page' => null]);
        }
        function pembelianSortIcon($col) {
            if (request('sort') !== $col) return '<i class="bi bi-arrow-down-up text-muted"></i>';
            return request('dir', 'desc') === 'asc' ? '<i class="bi bi-arrow-up"></i>' : '<i class="bi bi-arrow-down"></i>';
        }
    }
@endphp

@section('content')
<h4 class="fw-bold mb-4"><i class="bi bi-bag-plus me-2"></i>Pembelian</h4>

{{-- Form Pembelian --}}
<div class="card mb-4">
    <div class="card-header fw-bold"><i class="bi bi-plus-lg me-2"></i>Catat Pembelian Baru</div>
    <div class="card-body">
        <form method="POST" action="{{ route('pembelian.store') }}">
            @csrf
            <div class="row g-3">
                <div class="col-md-4">
                    <label class="form-label fw-semibold">Toko <span class="text-danger">*</span></label>
                    <select id="filterToko" class="form-select form-select-lg" onchange="filterProduk()">
                        <option value="">Semua Toko</option>
                        @foreach($tokos as $toko)
                            <option value="{{ $toko->id }}">{{ $toko->nama }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-4">
                    <label class="form-label fw-semibold">Produk <span class="text-danger">*</span></label>
                    <select name="produk_id" id="produkSelect" class="form-select form-select-lg" required>
                        <option value="">Pilih Produk</option>
                        @foreach($produks as $p)
                            <option value="{{ $p->id }}" data-toko="{{ $p->toko_id }}" data-kategori="{{ $p->kategori->nama }}">
                                {{ $p->nama }} — {{ $p->toko->nama }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-4">
                    <label class="form-label fw-semibold">Kategori</label>
                    <input type="text" id="kategoriDisplay" class="form-control form-control-lg" disabled placeholder="Otomatis dari produk">
                </div>
                <div class="col-md-3">
                    <label class="form-label fw-semibold">Qty <span class="text-danger">*</span></label>
                    <input type="number" name="jumlah" class="form-control form-control-lg" required min="1" value="{{ old('jumlah') }}">
                </div>
                <div class="col-md-3">
                    <label class="form-label fw-semibold">Nominal / Satuan (Rp) <span class="text-danger">*</span></label>
                    <input type="number" name="harga_beli" class="form-control form-control-lg" required min="0" value="{{ old('harga_beli') }}" placeholder="Harga beli per unit">
                </div>
                <div class="col-md-3">
                    <label class="form-label fw-semibold">Supplier <span class="text-danger">*</span></label>
                    <input type="text" name="supplier" class="form-control form-control-lg" value="{{ old('supplier', 'Gudang Pusat') }}" required>
                </div>
                <div class="col-md-3">
                    <label class="form-label fw-semibold">Tanggal <span class="text-danger">*</span></label>
                    <input type="date" name="tanggal" class="form-control form-control-lg" value="{{ old('tanggal', date('Y-m-d')) }}" required>
                </div>
                <div class="col-md-12">
                    <label class="form-label fw-semibold">Catatan</label>
                    <input type="text" name="catatan" class="form-control" placeholder="Opsional" value="{{ old('catatan') }}">
                </div>
                <div class="col-12">
                    <button type="submit" class="btn btn-gold btn-lg"><i class="bi bi-check-lg me-1"></i>Simpan Pembelian</button>
                </div>
            </div>
        </form>
    </div>
</div>

{{-- Riwayat Pembelian --}}
<div class="card">
    <div class="card-header fw-bold"><i class="bi bi-clock-history me-2"></i>Riwayat Pembelian</div>

    {{-- Filter --}}
    <div class="card-body border-bottom">
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
                <label class="form-label fw-semibold">Dari Tanggal</label>
                <input type="date" name="start_date" class="form-control" value="{{ request('start_date') }}">
            </div>
            <div class="col-md-3">
                <label class="form-label fw-semibold">Sampai Tanggal</label>
                <input type="date" name="end_date" class="form-control" value="{{ request('end_date') }}">
            </div>
            <div class="col-md-3">
                <button class="btn btn-mj w-100"><i class="bi bi-search me-1"></i>Filter</button>
            </div>
        </form>
    </div>

    <div class="card-body p-0">
        <form id="bulkPembelianForm" method="POST" action="{{ route('pembelian.bulkDelete') }}">
            @csrf
            <div id="bulkPembelianActions" class="p-3 d-none">
                <button type="button" class="btn btn-danger" onclick="confirmPembelianDelete()">
                    <i class="bi bi-trash me-1"></i>Hapus Terpilih (<span id="pembelianCount">0</span>)
                </button>
                <button type="button" class="btn btn-secondary ms-2" onclick="cancelPembelianBulk()">Batal</button>
            </div>
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead>
                        <tr>
                            <th><input type="checkbox" class="form-check-input" onchange="togglePembelianAll(this)"></th>
                            <th><a href="{{ pembelianSortUrl('tanggal') }}" class="text-decoration-none">Tanggal {!! pembelianSortIcon('tanggal') !!}</a></th>
                            <th><a href="{{ pembelianSortUrl('produk') }}" class="text-decoration-none">Produk {!! pembelianSortIcon('produk') !!}</a></th>
                            <th><a href="{{ pembelianSortUrl('kategori') }}" class="text-decoration-none">Kategori {!! pembelianSortIcon('kategori') !!}</a></th>
                            <th><a href="{{ pembelianSortUrl('toko') }}" class="text-decoration-none">Toko {!! pembelianSortIcon('toko') !!}</a></th>
                            <th><a href="{{ pembelianSortUrl('jumlah') }}" class="text-decoration-none">Qty {!! pembelianSortIcon('jumlah') !!}</a></th>
                            <th><a href="{{ pembelianSortUrl('harga_beli') }}" class="text-decoration-none">Nominal {!! pembelianSortIcon('harga_beli') !!}</a></th>
                            <th>Total</th>
                            <th><a href="{{ pembelianSortUrl('supplier') }}" class="text-decoration-none">Supplier {!! pembelianSortIcon('supplier') !!}</a></th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($riwayats as $r)
                        <tr>
                            <td><input type="checkbox" name="ids[]" value="{{ $r->id }}" class="form-check-input pembelian-check" onchange="updatePembelianCount()"></td>
                            <td>{{ $r->tanggal->format('d/m/Y') }}</td>
                            <td class="fw-semibold">{{ $r->produk->nama }}</td>
                            <td>{{ $r->produk->kategori->nama }}</td>
                            <td>{{ $r->produk->toko->nama }}</td>
                            <td>{{ $r->jumlah }}</td>
                            <td>Rp {{ number_format($r->harga_beli, 0, ',', '.') }}</td>
                            <td class="fw-semibold">Rp {{ number_format($r->harga_beli * $r->jumlah, 0, ',', '.') }}</td>
                            <td>{{ $r->supplier }}</td>
                        </tr>
                        @empty
                        <tr><td colspan="9" class="text-center text-muted py-4">Belum ada riwayat pembelian.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </form>
    </div>
</div>
<div class="mt-3">{{ $riwayats->links() }}</div>

{{-- Delete Confirm Modal --}}
<div class="modal fade" id="confirmPembelianModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header"><h5 class="modal-title">Konfirmasi Hapus</h5><button type="button" class="btn-close" data-bs-dismiss="modal"></button></div>
            <div class="modal-body"><p>Yakin ingin menghapus <strong id="pembelianDeleteCount"></strong> riwayat pembelian terpilih?</p></div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                <button type="button" class="btn btn-danger" onclick="document.getElementById('bulkPembelianForm').submit()">Ya, Hapus</button>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
function filterProduk() {
    const tokoId = document.getElementById('filterToko').value;
    const sel = document.getElementById('produkSelect');
    Array.from(sel.options).forEach(opt => {
        if (!opt.value) return;
        opt.style.display = (!tokoId || opt.dataset.toko === tokoId) ? '' : 'none';
    });
    sel.value = '';
    document.getElementById('kategoriDisplay').value = '';
}
document.getElementById('produkSelect').addEventListener('change', function() {
    const opt = this.options[this.selectedIndex];
    document.getElementById('kategoriDisplay').value = opt.value ? opt.dataset.kategori : '';
});
function togglePembelianAll(el) { document.querySelectorAll('.pembelian-check').forEach(cb => cb.checked = el.checked); updatePembelianCount(); }
function updatePembelianCount() { const c = document.querySelectorAll('.pembelian-check:checked').length; document.getElementById('pembelianCount').textContent = c; document.getElementById('bulkPembelianActions').classList.toggle('d-none', c === 0); }
function cancelPembelianBulk() { document.querySelectorAll('.pembelian-check').forEach(cb => cb.checked = false); updatePembelianCount(); }
function confirmPembelianDelete() { document.getElementById('pembelianDeleteCount').textContent = document.querySelectorAll('.pembelian-check:checked').length; new bootstrap.Modal(document.getElementById('confirmPembelianModal')).show(); }
</script>
@endsection
