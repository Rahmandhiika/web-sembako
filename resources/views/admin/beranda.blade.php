@extends('layouts.app')
@section('title', 'Beranda - Master Jaya')
@section('sidebar') @include('layouts.sidebar-admin') @endsection
@section('mobile-nav')
    @include('layouts.sidebar-admin')
@endsection

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h4 class="fw-bold mb-0"><i class="bi bi-house-door me-2"></i>Beranda</h4>
</div>

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
            <div class="col-md-2">
                <label class="form-label fw-semibold">Dari</label>
                <input type="date" name="start_date" class="form-control" value="{{ request('start_date', date('Y-m-d')) }}">
            </div>
            <div class="col-md-2">
                <label class="form-label fw-semibold">Sampai</label>
                <input type="date" name="end_date" class="form-control" value="{{ request('end_date', date('Y-m-d')) }}">
            </div>
            <div class="col-md-2">
                <button class="btn btn-mj w-100"><i class="bi bi-search me-1"></i>Terapkan</button>
            </div>
            @endif
        </form>
    </div>
</div>

{{-- Summary Cards --}}
<div class="row g-3 mb-4">
    <div class="col-md-3 col-6">
        <div class="card card-summary p-3">
            <div class="card-title">Total Produk</div>
            <div class="card-value">{{ number_format($totalProduk) }}</div>
        </div>
    </div>
    <div class="col-md-3 col-6">
        <div class="card card-summary p-3">
            <div class="card-title">Nilai Stok Saat Ini</div>
            <div class="card-value">Rp {{ number_format($nilaiStok, 0, ',', '.') }}</div>
        </div>
    </div>
    <div class="col-md-3 col-6">
        <div class="card card-summary p-3">
            <div class="card-title">Transaksi Periode Ini</div>
            <div class="card-value">{{ number_format($transaksiHariIni) }}</div>
        </div>
    </div>
    <div class="col-md-3 col-6">
        <div class="card card-summary p-3">
            <div class="card-title">Laba Periode Ini</div>
            <div class="card-value text-success">Rp {{ number_format($labaHariIni, 0, ',', '.') }}</div>
        </div>
    </div>
    <div class="col-md-4 col-6">
        <div class="card card-summary p-3">
            <div class="card-title">Omzet Bulan Ini</div>
            <div class="card-value">Rp {{ number_format($omzetBulan, 0, ',', '.') }}</div>
        </div>
    </div>
    <div class="col-md-4 col-6">
        <div class="card card-summary p-3" style="border-left-color: var(--mj-red);">
            <div class="card-title">Utang Aktif</div>
            <div class="card-value text-danger">Rp {{ number_format($utangAktif, 0, ',', '.') }}</div>
        </div>
    </div>
    <div class="col-md-4 col-6">
        <div class="card card-summary p-3" style="border-left-color: var(--mj-red);">
            <div class="card-title">Piutang Aktif</div>
            <div class="card-value text-warning">Rp {{ number_format($piutangAktif, 0, ',', '.') }}</div>
        </div>
    </div>
</div>

<div class="row g-4">
    {{-- Stok Menipis --}}
    <div class="col-md-6">
        <div class="card">
            <div class="card-header fw-bold"><i class="bi bi-exclamation-triangle me-2 text-warning"></i>Stok Menipis / Habis</div>
            <div class="card-body p-0">
                @if($stokMenipis->count())
                <div class="table-responsive">
                    <table class="table table-sm mb-0">
                        <thead><tr><th>Produk</th><th>Toko</th><th>Stok</th><th>Status</th></tr></thead>
                        <tbody>
                            @foreach($stokMenipis as $p)
                            <tr>
                                <td>{{ $p->nama }}</td>
                                <td>{{ $p->toko->nama }}</td>
                                <td>{{ $p->stok }}</td>
                                <td><span class="badge {{ $p->stok <= 0 ? 'badge-habis' : 'badge-menipis' }}">{{ $p->status_stok }}</span></td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                @else
                <p class="text-muted p-3 mb-0">Semua stok aman.</p>
                @endif
            </div>
        </div>
    </div>

    {{-- Transaksi Terbaru --}}
    <div class="col-md-6">
        <div class="card">
            <div class="card-header fw-bold"><i class="bi bi-clock-history me-2"></i>Transaksi Terbaru</div>
            <div class="card-body p-0">
                @if($transaksiTerbaru->count())
                <div class="table-responsive">
                    <table class="table table-sm mb-0">
                        <thead><tr><th>Tanggal</th><th>Produk</th><th>Toko</th><th>Nominal</th></tr></thead>
                        <tbody>
                            @foreach($transaksiTerbaru as $t)
                            <tr>
                                <td>{{ $t->tanggal->format('d/m/Y') }}</td>
                                <td>{{ $t->produk->nama }}</td>
                                <td>{{ $t->toko->nama }}</td>
                                <td>Rp {{ number_format($t->subtotal, 0, ',', '.') }}</td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                @else
                <p class="text-muted p-3 mb-0">Belum ada transaksi.</p>
                @endif
            </div>
        </div>
    </div>

    {{-- Utang Jatuh Tempo --}}
    <div class="col-md-6">
        <div class="card">
            <div class="card-header fw-bold"><i class="bi bi-calendar-x me-2 text-danger"></i>Utang Jatuh Tempo (7 Hari)</div>
            <div class="card-body p-0">
                @if($utangJatuhTempo->count())
                <div class="table-responsive">
                    <table class="table table-sm mb-0">
                        <thead><tr><th>Nama</th><th>Sisa</th><th>Jatuh Tempo</th></tr></thead>
                        <tbody>
                            @foreach($utangJatuhTempo as $u)
                            <tr class="{{ $u->jatuh_tempo < now() ? 'table-danger' : '' }}">
                                <td>{{ $u->nama }}</td>
                                <td>Rp {{ number_format($u->sisa, 0, ',', '.') }}</td>
                                <td>{{ $u->jatuh_tempo->format('d/m/Y') }}</td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                @else
                <p class="text-muted p-3 mb-0">Tidak ada utang jatuh tempo.</p>
                @endif
            </div>
        </div>
    </div>

    {{-- Piutang Jatuh Tempo --}}
    <div class="col-md-6">
        <div class="card">
            <div class="card-header fw-bold"><i class="bi bi-calendar-x me-2 text-warning"></i>Piutang Jatuh Tempo (7 Hari)</div>
            <div class="card-body p-0">
                @if($piutangJatuhTempo->count())
                <div class="table-responsive">
                    <table class="table table-sm mb-0">
                        <thead><tr><th>Pelanggan</th><th>Toko</th><th>Sisa</th><th>Jatuh Tempo</th></tr></thead>
                        <tbody>
                            @foreach($piutangJatuhTempo as $pi)
                            <tr class="{{ $pi->jatuh_tempo < now() ? 'table-danger' : '' }}">
                                <td>{{ $pi->nama_pelanggan }}</td>
                                <td>{{ $pi->toko->nama }}</td>
                                <td>Rp {{ number_format($pi->sisa, 0, ',', '.') }}</td>
                                <td>{{ $pi->jatuh_tempo->format('d/m/Y') }}</td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                @else
                <p class="text-muted p-3 mb-0">Tidak ada piutang jatuh tempo.</p>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection
