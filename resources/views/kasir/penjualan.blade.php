@extends('layouts.app')
@section('title', 'Penjualan - Master Jaya')
@section('sidebar') @include('layouts.sidebar-kasir') @endsection
@section('mobile-nav') @include('layouts.sidebar-kasir') @endsection

@section('content')
<h4 class="fw-bold mb-4"><i class="bi bi-cart-plus me-2"></i>Penjualan — {{ auth()->user()->toko->nama }}</h4>

{{-- Form Penjualan --}}
<div class="card mb-4">
    <div class="card-header fw-bold"><i class="bi bi-plus-lg me-2"></i>Catat Penjualan Baru</div>
    <div class="card-body">
        <form method="POST" action="{{ route('penjualan.store') }}">
            @csrf
            <div class="row g-3">
                <div class="col-md-6">
                    <label class="form-label fw-semibold">Produk <span class="text-danger">*</span></label>
                    <select name="produk_id" id="produkSelect" class="form-select form-select-lg" required onchange="updateHarga()">
                        <option value="">Pilih Produk</option>
                        @foreach($produks as $p)
                            <option value="{{ $p->id }}" data-harga="{{ $p->harga_jual }}" data-stok="{{ $p->stok }}">
                                {{ $p->nama }} (Stok: {{ $p->stok }})
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="form-label fw-semibold">Jumlah <span class="text-danger">*</span></label>
                    <input type="number" name="jumlah" id="jumlahInput" class="form-control form-control-lg" required min="1" value="{{ old('jumlah', 1) }}">
                    <small class="text-muted">Stok tersedia: <span id="stokInfo">-</span></small>
                </div>
                <div class="col-md-3">
                    <label class="form-label fw-semibold">Harga Jual / Satuan (Rp) <span class="text-danger">*</span></label>
                    <input type="number" name="harga_jual" id="hargaInput" class="form-control form-control-lg" required min="0" value="{{ old('harga_jual') }}">
                    <small class="text-muted">Bisa diubah (nego)</small>
                </div>
                <div class="col-md-4">
                    <label class="form-label fw-semibold">Tanggal Jual <span class="text-danger">*</span></label>
                    <input type="date" name="tanggal" class="form-control form-control-lg" value="{{ old('tanggal', date('Y-m-d')) }}" required>
                </div>
                <div class="col-md-4">
                    <label class="form-label fw-semibold">Nama Kasir <span class="text-danger">*</span></label>
                    <input type="text" name="nama_kasir" class="form-control form-control-lg" required value="{{ old('nama_kasir') }}" placeholder="Nama yang bertugas">
                </div>
                <div class="col-md-4">
                    <label class="form-label fw-semibold">Catatan</label>
                    <input type="text" name="catatan" class="form-control form-control-lg" placeholder="Opsional" value="{{ old('catatan') }}">
                </div>

                {{-- Piutang checkbox --}}
                <div class="col-12">
                    <div class="form-check form-check-lg mt-2">
                        <input class="form-check-input" type="checkbox" name="is_piutang" id="isPiutang" value="1" {{ old('is_piutang') ? 'checked' : '' }} onchange="togglePiutangFields()">
                        <label class="form-check-label fw-semibold" for="isPiutang">
                            <i class="bi bi-person-lines-fill me-1"></i> Pelanggan berhutang (piutang)
                        </label>
                    </div>
                </div>

                {{-- Piutang fields --}}
                <div id="piutangFields" class="col-12 {{ old('is_piutang') ? '' : 'd-none' }}">
                    <div class="card border-warning">
                        <div class="card-body">
                            <h6 class="fw-bold text-warning mb-3"><i class="bi bi-exclamation-circle me-1"></i>Data Piutang</h6>
                            <div class="row g-3">
                                <div class="col-md-4">
                                    <label class="form-label fw-semibold">Nama Pelanggan <span class="text-danger">*</span></label>
                                    <input type="text" name="piutang_nama" class="form-control form-control-lg" value="{{ old('piutang_nama') }}" placeholder="Nama yang berhutang">
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label fw-semibold">Jatuh Tempo <span class="text-danger">*</span></label>
                                    <input type="date" name="piutang_jatuh_tempo" class="form-control form-control-lg" value="{{ old('piutang_jatuh_tempo') }}">
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label fw-semibold">Keterangan</label>
                                    <input type="text" name="piutang_keterangan" class="form-control form-control-lg" value="{{ old('piutang_keterangan') }}" placeholder="Opsional">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-12">
                    <button type="submit" class="btn btn-gold btn-lg px-5">
                        <i class="bi bi-check-lg me-2"></i>Simpan Penjualan
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>

{{-- Filter Riwayat Hari Ini --}}
<div class="card mb-4">
    <div class="card-header fw-bold"><i class="bi bi-clock-history me-2"></i>Penjualan Hari Ini</div>
    <div class="card-body">
        <form method="GET" class="row g-3 align-items-end">
            <div class="col-md-3">
                <label class="form-label fw-semibold">Dari Tanggal</label>
                <input type="date" name="start_date" class="form-control" value="{{ request('start_date', date('Y-m-d')) }}">
            </div>
            <div class="col-md-3">
                <label class="form-label fw-semibold">Sampai Tanggal</label>
                <input type="date" name="end_date" class="form-control" value="{{ request('end_date', date('Y-m-d')) }}">
            </div>
            <div class="col-md-2">
                <button class="btn btn-mj w-100"><i class="bi bi-search me-1"></i>Filter</button>
            </div>
        </form>
    </div>
</div>

{{-- Penjualan list --}}
<div class="card mb-4">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead><tr><th>Tanggal</th><th>Produk</th><th>Jumlah</th><th>Harga</th><th>Subtotal</th><th>Kasir</th><th>Status</th></tr></thead>
                <tbody>
                    @forelse($penjualanHariIni as $pj)
                    <tr class="{{ $pj->status == 'dibatalkan' ? 'table-secondary' : '' }}">
                        <td>{{ $pj->tanggal->format('d/m/Y') }}</td>
                        <td>{{ $pj->produk->nama }}</td>
                        <td>{{ $pj->jumlah }}</td>
                        <td>Rp {{ number_format($pj->harga_jual, 0, ',', '.') }}</td>
                        <td>Rp {{ number_format($pj->subtotal, 0, ',', '.') }}</td>
                        <td>{{ $pj->nama_kasir }}</td>
                        <td><span class="badge {{ $pj->status == 'aktif' ? 'bg-success' : 'bg-secondary' }}">{{ ucfirst($pj->status) }}</span></td>
                    </tr>
                    @empty
                    <tr><td colspan="7" class="text-center text-muted py-4">Belum ada penjualan pada periode ini.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

{{-- Daftar Produk & Stok --}}
<div class="card">
    <div class="card-header fw-bold"><i class="bi bi-box-seam me-2"></i>Daftar Produk & Stok</div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead><tr><th>Produk</th><th>Kategori</th><th>Stok</th><th>Harga Jual</th><th>Status</th></tr></thead>
                <tbody>
                    @foreach($produks as $p)
                    <tr>
                        <td class="fw-semibold">{{ $p->nama }}</td>
                        <td>{{ $p->kategori->nama }}</td>
                        <td>{{ $p->stok }}</td>
                        <td>Rp {{ number_format($p->harga_jual, 0, ',', '.') }}</td>
                        <td><span class="badge {{ $p->status_stok == 'Habis' ? 'badge-habis' : ($p->status_stok == 'Menipis' ? 'badge-menipis' : 'badge-normal') }}">{{ $p->status_stok }}</span></td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
function updateHarga() {
    const sel = document.getElementById('produkSelect');
    const opt = sel.options[sel.selectedIndex];
    if (opt.value) {
        document.getElementById('hargaInput').value = opt.dataset.harga;
        document.getElementById('stokInfo').textContent = opt.dataset.stok;
    } else {
        document.getElementById('hargaInput').value = '';
        document.getElementById('stokInfo').textContent = '-';
    }
}
function togglePiutangFields() {
    document.getElementById('piutangFields').classList.toggle('d-none', !document.getElementById('isPiutang').checked);
}
</script>
@endsection
