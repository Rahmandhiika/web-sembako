@extends('layouts.app')
@section('title', 'Laporan - Master Jaya')
@section('sidebar') @include('layouts.sidebar-admin') @endsection
@section('mobile-nav') @include('layouts.sidebar-admin') @endsection

@section('content')
<h4 class="fw-bold mb-4"><i class="bi bi-graph-up me-2"></i>Laporan</h4>

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
                <label class="form-label fw-semibold">Periode</label>
                <select name="periode" class="form-select" onchange="this.form.submit()">
                    <option value="hari" {{ $periode == 'hari' ? 'selected' : '' }}>Hari Ini</option>
                    <option value="minggu" {{ $periode == 'minggu' ? 'selected' : '' }}>Minggu Ini</option>
                    <option value="bulan" {{ $periode == 'bulan' ? 'selected' : '' }}>Bulan Ini</option>
                    <option value="custom" {{ $periode == 'custom' ? 'selected' : '' }}>Pilih Tanggal</option>
                </select>
            </div>
            @if($periode == 'custom')
            <div class="col-md-2"><label class="form-label fw-semibold">Dari</label><input type="date" name="start_date" class="form-control" value="{{ $startDate }}"></div>
            <div class="col-md-2"><label class="form-label fw-semibold">Sampai</label><input type="date" name="end_date" class="form-control" value="{{ $endDate }}"></div>
            <div class="col-md-2"><button class="btn btn-mj w-100"><i class="bi bi-search me-1"></i>Terapkan</button></div>
            @endif
        </form>
    </div>
</div>

{{-- Metrik --}}
<div class="row g-3 mb-4">
    <div class="col-md-3 col-6"><div class="card card-summary p-3"><div class="card-title">Total Transaksi</div><div class="card-value">{{ number_format($totalTransaksi) }}</div></div></div>
    <div class="col-md-3 col-6"><div class="card card-summary p-3"><div class="card-title">Total Omzet</div><div class="card-value">Rp {{ number_format($totalOmzet, 0, ',', '.') }}</div></div></div>
    <div class="col-md-3 col-6"><div class="card card-summary p-3"><div class="card-title">Total Modal</div><div class="card-value">Rp {{ number_format($totalModal, 0, ',', '.') }}</div></div></div>
    <div class="col-md-3 col-6"><div class="card card-summary p-3"><div class="card-title">Laba Bersih</div><div class="card-value text-success">Rp {{ number_format($labaBersih, 0, ',', '.') }}</div></div></div>
    <div class="col-md-4 col-6"><div class="card card-summary p-3"><div class="card-title">Margin Laba</div><div class="card-value">{{ $marginLaba }}%</div></div></div>
    <div class="col-md-4 col-6"><div class="card card-summary p-3" style="border-left-color:var(--mj-red)"><div class="card-title">Utang Aktif</div><div class="card-value text-danger">Rp {{ number_format($utangAktif, 0, ',', '.') }}</div></div></div>
    <div class="col-md-4 col-6"><div class="card card-summary p-3" style="border-left-color:var(--mj-red)"><div class="card-title">Piutang Aktif</div><div class="card-value text-warning">Rp {{ number_format($piutangAktif, 0, ',', '.') }}</div></div></div>
</div>

{{-- Laba per Produk --}}
<div class="card mb-4">
    <div class="card-header fw-bold"><i class="bi bi-bar-chart me-2"></i>Laba per Produk</div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead><tr><th>Produk</th><th>Toko</th><th>Terjual</th><th>Omzet</th><th>Modal</th><th>Laba</th></tr></thead>
                <tbody>
                    @forelse($labaPerProduk as $l)
                    <tr>
                        <td class="fw-semibold">{{ $l->produk_nama }}</td>
                        <td>{{ $l->toko_nama }}</td>
                        <td>{{ $l->total_terjual }}</td>
                        <td>Rp {{ number_format($l->omzet, 0, ',', '.') }}</td>
                        <td>Rp {{ number_format($l->modal, 0, ',', '.') }}</td>
                        <td class="{{ $l->laba >= 0 ? 'text-success' : 'text-danger' }} fw-bold">Rp {{ number_format($l->laba, 0, ',', '.') }}</td>
                    </tr>
                    @empty
                    <tr><td colspan="6" class="text-center text-muted py-4">Belum ada data penjualan pada periode ini.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

{{-- Detail Penjualan --}}
<div class="card">
    <div class="card-header fw-bold"><i class="bi bi-receipt me-2"></i>Detail Penjualan Periode Ini</div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead><tr><th>Tanggal</th><th>Toko</th><th>Kasir</th><th>Produk</th><th>Jumlah</th><th>Harga Jual</th><th>Subtotal</th><th>Status</th></tr></thead>
                <tbody>
                    @forelse($detailPenjualan as $d)
                    <tr class="{{ $d->status == 'dibatalkan' ? 'table-secondary' : '' }}">
                        <td>{{ $d->tanggal->format('d/m/Y') }}</td>
                        <td>{{ $d->toko->nama }}</td>
                        <td>{{ $d->nama_kasir }}</td>
                        <td>{{ $d->produk->nama }}</td>
                        <td>{{ $d->jumlah }}</td>
                        <td>Rp {{ number_format($d->harga_jual, 0, ',', '.') }}</td>
                        <td>Rp {{ number_format($d->subtotal, 0, ',', '.') }}</td>
                        <td><span class="badge {{ $d->status == 'aktif' ? 'bg-success' : 'bg-secondary' }}">{{ ucfirst($d->status) }}</span></td>
                    </tr>
                    @empty
                    <tr><td colspan="8" class="text-center text-muted py-4">Belum ada data.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
<div class="mt-3">{{ $detailPenjualan->links() }}</div>

{{-- Reset Data --}}
<div class="card mt-5 border-danger">
    <div class="card-body text-center py-4">
        <h5 class="text-danger fw-bold mb-2"><i class="bi bi-exclamation-triangle me-2"></i>Reset Semua Data</h5>
        <p class="text-muted mb-3">Menghapus seluruh data transaksi, produk, restock, utang, dan piutang lalu mengisi ulang dengan data awal (dummy). Akun pengguna tetap dipertahankan.</p>
        <button class="btn btn-outline-danger btn-lg" data-bs-toggle="modal" data-bs-target="#resetModal">
            <i class="bi bi-arrow-counterclockwise me-2"></i>Reset Semua Data
        </button>
    </div>
</div>

<div class="modal fade" id="resetModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form method="POST" action="{{ route('reset.data') }}">
                @csrf
                <div class="modal-header bg-danger text-white">
                    <h5 class="modal-title"><i class="bi bi-exclamation-triangle me-2"></i>Konfirmasi Reset</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <p class="fw-bold">Yakin ingin mereset semua data?</p>
                    <p>Semua data berikut akan dihapus dan diganti dengan data dummy:</p>
                    <ul>
                        <li>Produk & Kategori</li>
                        <li>Restock</li>
                        <li>Penjualan & Riwayat</li>
                        <li>Utang & Piutang (termasuk pembayaran)</li>
                    </ul>
                    <p class="text-danger"><strong>Tindakan ini tidak dapat dibatalkan.</strong></p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-danger"><i class="bi bi-arrow-counterclockwise me-1"></i>Ya, Reset Semua</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
