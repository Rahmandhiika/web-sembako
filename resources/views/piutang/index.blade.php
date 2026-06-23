@extends('layouts.app')
@section('title', 'Piutang - Master Jaya')
@section('sidebar') @include('layouts.sidebar-kasir') @endsection
@section('mobile-nav') @include('layouts.sidebar-kasir') @endsection

@section('content')
<h4 class="fw-bold mb-4"><i class="bi bi-person-lines-fill me-2"></i>Piutang — {{ auth()->user()->toko->nama }}</h4>

{{-- Ringkasan --}}
<div class="row g-3 mb-4">
    <div class="col-md-3 col-6"><div class="card card-summary p-3"><div class="card-title">Total Sisa Piutang</div><div class="card-value text-warning">Rp {{ number_format($totalSisa, 0, ',', '.') }}</div></div></div>
    <div class="col-md-3 col-6"><div class="card card-summary p-3"><div class="card-title">Belum Lunas</div><div class="card-value">{{ $belumLunas }}</div></div></div>
    <div class="col-md-3 col-6"><div class="card card-summary p-3"><div class="card-title">Sudah Lunas</div><div class="card-value text-success">{{ $sudahLunas }}</div></div></div>
    <div class="col-md-3 col-6"><div class="card card-summary p-3" style="border-left-color:var(--mj-red)"><div class="card-title">Lewat Jatuh Tempo</div><div class="card-value text-danger">{{ $lewatJatuhTempo }}</div></div></div>
</div>

{{-- Tambah Piutang --}}
<div class="card mb-4">
    <div class="card-header fw-bold"><i class="bi bi-plus-lg me-2"></i>Tambah Piutang Baru</div>
    <div class="card-body">
        <form method="POST" action="{{ route('piutang.store') }}">
            @csrf
            <div class="row g-3">
                <div class="col-md-4"><label class="form-label fw-semibold">Nama Pelanggan <span class="text-danger">*</span></label><input type="text" name="nama_pelanggan" class="form-control form-control-lg" required></div>
                <div class="col-md-4"><label class="form-label fw-semibold">Nominal (Rp) <span class="text-danger">*</span></label><input type="number" name="nominal" class="form-control form-control-lg" required min="1"></div>
                <div class="col-md-4"><label class="form-label fw-semibold">Tanggal <span class="text-danger">*</span></label><input type="date" name="tanggal" class="form-control form-control-lg" value="{{ date('Y-m-d') }}" required></div>
                <div class="col-md-4"><label class="form-label fw-semibold">Jatuh Tempo <span class="text-danger">*</span></label><input type="date" name="jatuh_tempo" class="form-control form-control-lg" required></div>
                <div class="col-md-8"><label class="form-label fw-semibold">Keterangan</label><input type="text" name="keterangan" class="form-control form-control-lg" placeholder="Opsional"></div>
                <div class="col-12"><button type="submit" class="btn btn-gold btn-lg"><i class="bi bi-check-lg me-1"></i>Simpan Piutang</button></div>
            </div>
        </form>
    </div>
</div>

{{-- Daftar --}}
<div class="card">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead><tr><th>Pelanggan</th><th>Nominal</th><th>Sisa</th><th>Jatuh Tempo</th><th>Status</th><th>Aksi</th></tr></thead>
                <tbody>
                    @forelse($piutangs as $pi)
                    <tr class="{{ $pi->status == 'Lewat Jatuh Tempo' ? 'table-danger' : '' }}">
                        <td class="fw-semibold">{{ $pi->nama_pelanggan }}</td>
                        <td>Rp {{ number_format($pi->nominal, 0, ',', '.') }}</td>
                        <td>Rp {{ number_format($pi->sisa, 0, ',', '.') }}</td>
                        <td>{{ $pi->jatuh_tempo->format('d/m/Y') }}</td>
                        <td><span class="badge {{ $pi->status == 'Lunas' ? 'bg-success' : ($pi->status == 'Lewat Jatuh Tempo' ? 'bg-danger' : 'bg-warning text-dark') }}">{{ $pi->status }}</span></td>
                        <td>
                            @if($pi->sisa > 0)
                            <button class="btn btn-sm btn-gold" data-bs-toggle="modal" data-bs-target="#bayarModal{{ $pi->id }}"><i class="bi bi-wallet2 me-1"></i>Bayar</button>
                            @endif
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="6" class="text-center text-muted py-4">Belum ada data piutang.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
<div class="mt-3">{{ $piutangs->links() }}</div>

{{-- Bayar Modals --}}
@foreach($piutangs as $pi)
@if($pi->sisa > 0)
<div class="modal fade" id="bayarModal{{ $pi->id }}" tabindex="-1">
    <div class="modal-dialog"><div class="modal-content">
        <form method="POST" action="{{ route('piutang.bayar', $pi) }}">@csrf
            <div class="modal-header"><h5 class="modal-title">Bayar Piutang: {{ $pi->nama_pelanggan }}</h5><button type="button" class="btn-close" data-bs-dismiss="modal"></button></div>
            <div class="modal-body">
                <p>Total Piutang: <strong>Rp {{ number_format($pi->nominal, 0, ',', '.') }}</strong></p>
                <p>Sisa: <strong class="text-warning">Rp {{ number_format($pi->sisa, 0, ',', '.') }}</strong></p>
                @if($pi->pembayarans->count())
                <div class="mb-3">
                    <small class="fw-semibold text-muted">Riwayat Pembayaran:</small>
                    <ul class="list-unstyled ms-2 mt-1">
                        @foreach($pi->pembayarans as $pb)
                        <li><small>{{ $pb->tanggal->format('d/m/Y') }} — Rp {{ number_format($pb->nominal, 0, ',', '.') }}</small></li>
                        @endforeach
                    </ul>
                </div>
                @endif
                <hr>
                <div class="mb-3">
                    <label class="form-label fw-semibold">Nominal Bayar (Rp) <span class="text-danger">*</span></label>
                    <input type="number" name="nominal" class="form-control form-control-lg" required min="1" max="{{ $pi->sisa }}" placeholder="Maks: {{ number_format($pi->sisa, 0, ',', '.') }}" id="nominalPiutangKasir{{ $pi->id }}">
                </div>
                <div class="mb-3">
                    <label class="form-label fw-semibold">Tanggal <span class="text-danger">*</span></label>
                    <input type="date" name="tanggal" class="form-control form-control-lg" value="{{ date('Y-m-d') }}" required>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                <button type="button" class="btn btn-success btn-lg" onclick="document.getElementById('nominalPiutangKasir{{ $pi->id }}').value={{ $pi->sisa }};this.closest('form').submit()"><i class="bi bi-check-all me-1"></i>Bayar Lunas</button>
                <button type="submit" class="btn btn-gold btn-lg"><i class="bi bi-wallet2 me-1"></i>Bayar</button>
            </div>
        </form>
    </div></div>
</div>
@endif
@endforeach
@endsection
