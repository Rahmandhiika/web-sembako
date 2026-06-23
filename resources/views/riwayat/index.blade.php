@extends('layouts.app')
@section('title', 'Riwayat Penjualan - Master Jaya')
@section('sidebar')
    @if($isKasir) @include('layouts.sidebar-kasir') @else @include('layouts.sidebar-admin') @endif
@endsection
@section('mobile-nav')
    @if($isKasir) @include('layouts.sidebar-kasir') @else @include('layouts.sidebar-admin') @endif
@endsection

@section('content')
<h4 class="fw-bold mb-4">
    <i class="bi bi-clock-history me-2"></i>Riwayat Penjualan
    @if($isKasir) — {{ auth()->user()->toko->nama }} @endif
</h4>

{{-- Filter (admin only) --}}
@if(!$isKasir)
<div class="card mb-4">
    <div class="card-body">
        <form method="GET" class="row g-3 align-items-end">
            <div class="col-md-4">
                <label class="form-label fw-semibold">Toko</label>
                <select name="toko_id" class="form-select" onchange="this.form.submit()">
                    <option value="">Semua Toko</option>
                    @foreach($tokos as $toko)
                        <option value="{{ $toko->id }}" {{ $tokoId == $toko->id ? 'selected' : '' }}>{{ $toko->nama }}</option>
                    @endforeach
                </select>
            </div>
        </form>
    </div>
</div>
@endif

<div class="card">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead>
                    <tr>
                        <th>Tanggal</th>
                        <th>Produk</th>
                        <th>Toko</th>
                        <th>Jumlah</th>
                        <th>Harga Jual</th>
                        <th>Subtotal</th>
                        <th>Kasir</th>
                        <th>Status</th>
                        @if($isKasir)<th>Aksi</th>@endif
                    </tr>
                </thead>
                <tbody>
                    @forelse($penjualans as $p)
                    <tr class="{{ $p->status == 'dibatalkan' ? 'table-secondary' : '' }}">
                        <td>{{ $p->tanggal->format('d/m/Y') }}</td>
                        <td>{{ $p->produk->nama }}</td>
                        <td>{{ $p->toko->nama }}</td>
                        <td>{{ $p->jumlah }}</td>
                        <td>Rp {{ number_format($p->harga_jual, 0, ',', '.') }}</td>
                        <td>Rp {{ number_format($p->subtotal, 0, ',', '.') }}</td>
                        <td>{{ $p->nama_kasir }}</td>
                        <td>
                            <span class="badge {{ $p->status == 'aktif' ? 'bg-success' : 'bg-secondary' }}">
                                {{ ucfirst($p->status) }}
                            </span>
                        </td>
                        @if($isKasir)
                        <td>
                            @if($p->status == 'aktif')
                            <button class="btn btn-sm btn-danger" data-bs-toggle="modal" data-bs-target="#batalModal" onclick="setBatal({{ $p->id }}, '{{ $p->produk->nama }}')">
                                <i class="bi bi-x-circle me-1"></i>Batalkan
                            </button>
                            @else
                            <span class="text-muted">-</span>
                            @endif
                        </td>
                        @endif
                    </tr>
                    @empty
                    <tr><td colspan="9" class="text-center text-muted py-4">Belum ada riwayat penjualan.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
<div class="mt-3">{{ $penjualans->links() }}</div>

@if($isKasir)
{{-- Confirm Batal Modal --}}
<div class="modal fade" id="batalModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form method="POST" id="batalForm">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title text-danger"><i class="bi bi-exclamation-triangle me-2"></i>Konfirmasi Pembatalan</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <p>Yakin ingin membatalkan penjualan <strong id="batalProduk"></strong>?</p>
                    <p class="text-muted">Stok akan otomatis dikembalikan. Transaksi tetap tercatat dengan status "Dibatalkan".</p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tidak</button>
                    <button type="submit" class="btn btn-danger"><i class="bi bi-x-circle me-1"></i>Ya, Batalkan</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endif
@endsection

@section('scripts')
@if($isKasir)
<script>
function setBatal(id, produk) {
    document.getElementById('batalForm').action = '/riwayat/' + id + '/batalkan';
    document.getElementById('batalProduk').textContent = produk;
}
</script>
@endif
@endsection
