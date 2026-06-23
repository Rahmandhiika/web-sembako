{{-- This partial is included in admin utang-piutang page (piutang tab) --}}
@php
    $piutangTokoId = request('piutang_toko_id');
    $piutangs = \App\Models\Piutang::with(['toko', 'pembayarans'])
        ->when($piutangTokoId, fn($q) => $q->where('toko_id', $piutangTokoId))
        ->latest('tanggal')->latest('id')
        ->paginate(20, ['*'], 'piutang_page')
        ->withQueryString();
    $pTotalSisa = \App\Models\Piutang::when($piutangTokoId, fn($q) => $q->where('toko_id', $piutangTokoId))->where('sisa', '>', 0)->sum('sisa');
    $pBelumLunas = \App\Models\Piutang::when($piutangTokoId, fn($q) => $q->where('toko_id', $piutangTokoId))->where('sisa', '>', 0)->count();
    $pSudahLunas = \App\Models\Piutang::when($piutangTokoId, fn($q) => $q->where('toko_id', $piutangTokoId))->where('sisa', '<=', 0)->count();
    $pLewat = \App\Models\Piutang::when($piutangTokoId, fn($q) => $q->where('toko_id', $piutangTokoId))->where('sisa', '>', 0)->where('jatuh_tempo', '<', now()->toDateString())->count();
@endphp

{{-- Filter --}}
<div class="card mb-4">
    <div class="card-body">
        <form method="GET" class="row g-3 align-items-end">
            <input type="hidden" name="tab" value="piutang">
            <div class="col-md-4">
                <label class="form-label fw-semibold">Toko</label>
                <select name="piutang_toko_id" class="form-select" onchange="this.form.submit()">
                    <option value="">Semua Toko</option>
                    @foreach($tokos as $t)<option value="{{ $t->id }}" {{ $piutangTokoId == $t->id ? 'selected' : '' }}>{{ $t->nama }}</option>@endforeach
                </select>
            </div>
        </form>
    </div>
</div>

{{-- Ringkasan --}}
<div class="row g-3 mb-4">
    <div class="col-md-3 col-6"><div class="card card-summary p-3"><div class="card-title">Total Sisa Piutang</div><div class="card-value text-warning">Rp {{ number_format($pTotalSisa, 0, ',', '.') }}</div></div></div>
    <div class="col-md-3 col-6"><div class="card card-summary p-3"><div class="card-title">Belum Lunas</div><div class="card-value">{{ $pBelumLunas }}</div></div></div>
    <div class="col-md-3 col-6"><div class="card card-summary p-3"><div class="card-title">Sudah Lunas</div><div class="card-value text-success">{{ $pSudahLunas }}</div></div></div>
    <div class="col-md-3 col-6"><div class="card card-summary p-3" style="border-left-color:var(--mj-red)"><div class="card-title">Lewat Jatuh Tempo</div><div class="card-value text-danger">{{ $pLewat }}</div></div></div>
</div>

{{-- Tambah Piutang --}}
<div class="card mb-4">
    <div class="card-header fw-bold"><i class="bi bi-plus-lg me-2"></i>Tambah Piutang</div>
    <div class="card-body">
        <form method="POST" action="{{ route('piutang.store') }}">
            @csrf
            <div class="row g-3">
                <div class="col-md-3"><label class="form-label fw-semibold">Toko <span class="text-danger">*</span></label>
                    <select name="toko_id" class="form-select" required>@foreach($tokos as $t)<option value="{{ $t->id }}">{{ $t->nama }}</option>@endforeach</select>
                </div>
                <div class="col-md-3"><label class="form-label fw-semibold">Nama Pelanggan <span class="text-danger">*</span></label><input type="text" name="nama_pelanggan" class="form-control" required></div>
                <div class="col-md-3"><label class="form-label fw-semibold">Nominal (Rp) <span class="text-danger">*</span></label><input type="number" name="nominal" class="form-control" required min="1"></div>
                <div class="col-md-3"><label class="form-label fw-semibold">Tanggal <span class="text-danger">*</span></label><input type="date" name="tanggal" class="form-control" value="{{ date('Y-m-d') }}" required></div>
                <div class="col-md-3"><label class="form-label fw-semibold">Jatuh Tempo <span class="text-danger">*</span></label><input type="date" name="jatuh_tempo" class="form-control" required></div>
                <div class="col-md-6"><label class="form-label fw-semibold">Keterangan</label><input type="text" name="keterangan" class="form-control" placeholder="Opsional"></div>
                <div class="col-md-3 d-flex align-items-end"><button type="submit" class="btn btn-gold w-100"><i class="bi bi-check-lg me-1"></i>Simpan</button></div>
            </div>
        </form>
    </div>
</div>

{{-- Daftar Piutang --}}
<div class="card">
    <div class="card-body p-0">
        <form id="bulkPiutangForm" method="POST" action="{{ route('piutang.bulkDelete') }}">
            @csrf
            <div id="bulkPiutangActions" class="p-3 d-none">
                <button type="button" class="btn btn-danger" onclick="confirmPiutangDelete()"><i class="bi bi-trash me-1"></i>Hapus (<span id="piutangSelCount">0</span>)</button>
                <button type="button" class="btn btn-secondary ms-2" onclick="cancelPiutangBulk()">Batal</button>
            </div>
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead><tr><th><input type="checkbox" class="form-check-input" onchange="togglePiutangAll(this)"></th><th>Pelanggan</th><th>Toko</th><th>Nominal</th><th>Sisa</th><th>Jatuh Tempo</th><th>Status</th><th>Aksi</th></tr></thead>
                    <tbody>
                        @forelse($piutangs as $pi)
                        <tr class="{{ $pi->status == 'Lewat Jatuh Tempo' ? 'table-danger' : '' }}">
                            <td><input type="checkbox" name="ids[]" value="{{ $pi->id }}" class="form-check-input piutang-check" onchange="updatePiutangCount()"></td>
                            <td>{{ $pi->nama_pelanggan }}</td>
                            <td>{{ $pi->toko->nama }}</td>
                            <td>Rp {{ number_format($pi->nominal, 0, ',', '.') }}</td>
                            <td>Rp {{ number_format($pi->sisa, 0, ',', '.') }}</td>
                            <td>{{ $pi->jatuh_tempo->format('d/m/Y') }}</td>
                            <td><span class="badge {{ $pi->status == 'Lunas' ? 'bg-success' : ($pi->status == 'Lewat Jatuh Tempo' ? 'bg-danger' : 'bg-warning text-dark') }}">{{ $pi->status }}</span></td>
                            <td>
                                @if($pi->sisa > 0)
                                <button class="btn btn-sm btn-mj" data-bs-toggle="modal" data-bs-target="#bayarPiutangModal{{ $pi->id }}"><i class="bi bi-wallet2 me-1"></i>Bayar</button>
                                @endif
                            </td>
                        </tr>
                        @empty
                        <tr><td colspan="8" class="text-center text-muted py-4">Belum ada data piutang.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </form>
    </div>
</div>
<div class="mt-3">{{ $piutangs->links() }}</div>

{{-- Bayar Piutang Modals --}}
@foreach($piutangs as $pi)
@if($pi->sisa > 0)
<div class="modal fade" id="bayarPiutangModal{{ $pi->id }}" tabindex="-1">
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
                    <input type="number" name="nominal" class="form-control form-control-lg" required min="1" max="{{ $pi->sisa }}" placeholder="Maks: {{ number_format($pi->sisa, 0, ',', '.') }}" id="nominalPiutangAdmin{{ $pi->id }}">
                </div>
                <div class="mb-3">
                    <label class="form-label fw-semibold">Tanggal <span class="text-danger">*</span></label>
                    <input type="date" name="tanggal" class="form-control" value="{{ date('Y-m-d') }}" required>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                <button type="button" class="btn btn-success" onclick="document.getElementById('nominalPiutangAdmin{{ $pi->id }}').value={{ $pi->sisa }};this.closest('form').submit()"><i class="bi bi-check-all me-1"></i>Bayar Lunas</button>
                <button type="submit" class="btn btn-gold"><i class="bi bi-wallet2 me-1"></i>Bayar</button>
            </div>
        </form>
    </div></div>
</div>
@endif
@endforeach

{{-- Confirm Delete Piutang --}}
<div class="modal fade" id="confirmPiutangDeleteModal" tabindex="-1">
    <div class="modal-dialog"><div class="modal-content">
        <div class="modal-header"><h5 class="modal-title">Konfirmasi Hapus</h5><button type="button" class="btn-close" data-bs-dismiss="modal"></button></div>
        <div class="modal-body"><p>Yakin ingin menghapus <strong id="piutangDeleteCount"></strong> piutang terpilih?</p></div>
        <div class="modal-footer"><button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button><button type="button" class="btn btn-danger" onclick="document.getElementById('bulkPiutangForm').submit()">Ya, Hapus</button></div>
    </div></div>
</div>

<script>
function togglePiutangAll(el) { document.querySelectorAll('.piutang-check').forEach(cb => cb.checked = el.checked); updatePiutangCount(); }
function updatePiutangCount() { const c = document.querySelectorAll('.piutang-check:checked').length; document.getElementById('piutangSelCount').textContent = c; document.getElementById('bulkPiutangActions').classList.toggle('d-none', c === 0); }
function cancelPiutangBulk() { document.querySelectorAll('.piutang-check').forEach(cb => cb.checked = false); updatePiutangCount(); }
function confirmPiutangDelete() { document.getElementById('piutangDeleteCount').textContent = document.querySelectorAll('.piutang-check:checked').length; new bootstrap.Modal(document.getElementById('confirmPiutangDeleteModal')).show(); }
</script>
