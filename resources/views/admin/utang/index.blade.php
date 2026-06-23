@extends('layouts.app')
@section('title', 'Utang Piutang - Master Jaya')
@section('sidebar') @include('layouts.sidebar-admin') @endsection
@section('mobile-nav') @include('layouts.sidebar-admin') @endsection

@section('content')
<h4 class="fw-bold mb-4"><i class="bi bi-cash-stack me-2"></i>Utang Piutang</h4>

{{-- Sub-tabs --}}
<ul class="nav nav-tabs mb-4">
    <li class="nav-item">
        <a class="nav-link {{ $tab == 'utang' ? 'active' : '' }}" href="{{ route('utang-piutang.index', ['tab' => 'utang']) }}">
            <i class="bi bi-arrow-up-circle me-1"></i>Utang
        </a>
    </li>
    <li class="nav-item">
        <a class="nav-link {{ $tab == 'piutang' ? 'active' : '' }}" href="{{ route('utang-piutang.index', ['tab' => 'piutang']) }}">
            <i class="bi bi-arrow-down-circle me-1"></i>Piutang
        </a>
    </li>
</ul>

@if($tab == 'utang')
    {{-- Ringkasan --}}
    <div class="row g-3 mb-4">
        <div class="col-md-3 col-6"><div class="card card-summary p-3"><div class="card-title">Total Sisa Utang</div><div class="card-value text-danger">Rp {{ number_format($totalSisa, 0, ',', '.') }}</div></div></div>
        <div class="col-md-3 col-6"><div class="card card-summary p-3"><div class="card-title">Belum Lunas</div><div class="card-value">{{ $belumLunas }}</div></div></div>
        <div class="col-md-3 col-6"><div class="card card-summary p-3"><div class="card-title">Sudah Lunas</div><div class="card-value text-success">{{ $sudahLunas }}</div></div></div>
        <div class="col-md-3 col-6"><div class="card card-summary p-3" style="border-left-color:var(--mj-red)"><div class="card-title">Lewat Jatuh Tempo</div><div class="card-value text-danger">{{ $lewatJatuhTempo }}</div></div></div>
    </div>

    {{-- Tambah Utang --}}
    <div class="card mb-4">
        <div class="card-header fw-bold"><i class="bi bi-plus-lg me-2"></i>Tambah Utang</div>
        <div class="card-body">
            <form method="POST" action="{{ route('utang.store') }}">
                @csrf
                <div class="row g-3">
                    <div class="col-md-4"><label class="form-label fw-semibold">Nama <span class="text-danger">*</span></label><input type="text" name="nama" class="form-control" required placeholder="Contoh: Utang Beras Batch Juni"></div>
                    <div class="col-md-4"><label class="form-label fw-semibold">Supplier</label><input type="text" name="supplier" class="form-control" value="Gudang Pusat"></div>
                    <div class="col-md-4"><label class="form-label fw-semibold">Nominal (Rp) <span class="text-danger">*</span></label><input type="number" name="nominal" class="form-control" required min="1"></div>
                    <div class="col-md-3"><label class="form-label fw-semibold">Tanggal <span class="text-danger">*</span></label><input type="date" name="tanggal" class="form-control" value="{{ date('Y-m-d') }}" required></div>
                    <div class="col-md-3"><label class="form-label fw-semibold">Jatuh Tempo <span class="text-danger">*</span></label><input type="date" name="jatuh_tempo" class="form-control" required></div>
                    <div class="col-md-3"><label class="form-label fw-semibold">Tag Toko</label>
                        <select name="toko_id" class="form-select"><option value="">Semua Toko</option>@foreach($tokos as $t)<option value="{{ $t->id }}">{{ $t->nama }}</option>@endforeach</select>
                    </div>
                    <div class="col-md-3"><label class="form-label fw-semibold">Keterangan</label><input type="text" name="keterangan" class="form-control" placeholder="Opsional"></div>
                    <div class="col-12"><button type="submit" class="btn btn-gold"><i class="bi bi-check-lg me-1"></i>Simpan Utang</button></div>
                </div>
            </form>
        </div>
    </div>

    {{-- Daftar Utang --}}
    <div class="card">
        <div class="card-body p-0">
            <form id="bulkUtangForm" method="POST" action="{{ route('utang.bulkDelete') }}">
                @csrf
                <div id="bulkUtangActions" class="p-3 d-none">
                    <button type="button" class="btn btn-danger" onclick="confirmUtangDelete()"><i class="bi bi-trash me-1"></i>Hapus (<span id="utangSelCount">0</span>)</button>
                    <button type="button" class="btn btn-secondary ms-2" onclick="cancelUtangBulk()">Batal</button>
                </div>
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead><tr><th><input type="checkbox" class="form-check-input" onchange="toggleUtangAll(this)"></th><th>Nama</th><th>Supplier</th><th>Nominal</th><th>Sisa</th><th>Jatuh Tempo</th><th>Status</th><th>Aksi</th></tr></thead>
                        <tbody>
                            @forelse($utangs as $u)
                            <tr class="{{ $u->status == 'Lewat Jatuh Tempo' ? 'table-danger' : '' }}">
                                <td><input type="checkbox" name="ids[]" value="{{ $u->id }}" class="form-check-input utang-check" onchange="updateUtangCount()"></td>
                                <td>{{ $u->nama }}</td>
                                <td>{{ $u->supplier }}</td>
                                <td>Rp {{ number_format($u->nominal, 0, ',', '.') }}</td>
                                <td>Rp {{ number_format($u->sisa, 0, ',', '.') }}</td>
                                <td>{{ $u->jatuh_tempo->format('d/m/Y') }}</td>
                                <td><span class="badge {{ $u->status == 'Lunas' ? 'bg-success' : ($u->status == 'Lewat Jatuh Tempo' ? 'bg-danger' : 'bg-warning text-dark') }}">{{ $u->status }}</span></td>
                                <td>
                                    @if($u->sisa > 0)
                                    <button class="btn btn-sm btn-mj" data-bs-toggle="modal" data-bs-target="#bayarUtangModal{{ $u->id }}"><i class="bi bi-wallet2 me-1"></i>Bayar</button>
                                    @endif
                                </td>
                            </tr>
                            @empty
                            <tr><td colspan="8" class="text-center text-muted py-4">Belum ada data utang.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </form>
        </div>
    </div>
    <div class="mt-3">{{ $utangs->links() }}</div>

@else
    @include('piutang._admin_section')
@endif

{{-- Bayar Utang Modals --}}
@if($tab == 'utang')
@foreach($utangs as $u)
@if($u->sisa > 0)
<div class="modal fade" id="bayarUtangModal{{ $u->id }}" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form method="POST" action="{{ route('utang.bayar', $u) }}" id="formBayarUtang{{ $u->id }}">
                @csrf
                <div class="modal-header"><h5 class="modal-title">Bayar Utang: {{ $u->nama }}</h5><button type="button" class="btn-close" data-bs-dismiss="modal"></button></div>
                <div class="modal-body">
                    <p>Total Utang: <strong>Rp {{ number_format($u->nominal, 0, ',', '.') }}</strong></p>
                    <p>Sisa: <strong class="text-danger">Rp {{ number_format($u->sisa, 0, ',', '.') }}</strong></p>
                    @if($u->pembayarans->count())
                    <div class="mb-3">
                        <small class="fw-semibold text-muted">Riwayat Pembayaran:</small>
                        <ul class="list-unstyled ms-2 mt-1">
                            @foreach($u->pembayarans as $pb)
                            <li><small>{{ $pb->tanggal->format('d/m/Y') }} — Rp {{ number_format($pb->nominal, 0, ',', '.') }}</small></li>
                            @endforeach
                        </ul>
                    </div>
                    @endif
                    <hr>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Nominal Bayar (Rp) <span class="text-danger">*</span></label>
                        <input type="number" name="nominal" class="form-control form-control-lg" required min="1" max="{{ $u->sisa }}" placeholder="Maks: {{ number_format($u->sisa, 0, ',', '.') }}" id="nominalUtang{{ $u->id }}">
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Tanggal <span class="text-danger">*</span></label>
                        <input type="date" name="tanggal" class="form-control" value="{{ date('Y-m-d') }}" required>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="button" class="btn btn-success" onclick="document.getElementById('nominalUtang{{ $u->id }}').value={{ $u->sisa }};this.closest('form').submit()">
                        <i class="bi bi-check-all me-1"></i>Bayar Lunas
                    </button>
                    <button type="submit" class="btn btn-gold">
                        <i class="bi bi-wallet2 me-1"></i>Bayar
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endif
@endforeach
@endif

{{-- Confirm Delete --}}
<div class="modal fade" id="confirmUtangDeleteModal" tabindex="-1">
    <div class="modal-dialog"><div class="modal-content">
        <div class="modal-header"><h5 class="modal-title">Konfirmasi Hapus</h5><button type="button" class="btn-close" data-bs-dismiss="modal"></button></div>
        <div class="modal-body"><p>Yakin ingin menghapus <strong id="utangDeleteCount"></strong> utang terpilih?</p></div>
        <div class="modal-footer"><button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button><button type="button" class="btn btn-danger" onclick="document.getElementById('bulkUtangForm').submit()">Ya, Hapus</button></div>
    </div></div>
</div>
@endsection

@section('scripts')
<script>
function toggleUtangAll(el) { document.querySelectorAll('.utang-check').forEach(cb => cb.checked = el.checked); updateUtangCount(); }
function updateUtangCount() { const c = document.querySelectorAll('.utang-check:checked').length; document.getElementById('utangSelCount').textContent = c; document.getElementById('bulkUtangActions').classList.toggle('d-none', c === 0); }
function cancelUtangBulk() { document.querySelectorAll('.utang-check').forEach(cb => cb.checked = false); updateUtangCount(); }
function confirmUtangDelete() { document.getElementById('utangDeleteCount').textContent = document.querySelectorAll('.utang-check:checked').length; new bootstrap.Modal(document.getElementById('confirmUtangDeleteModal')).show(); }
</script>
@endsection
