@extends('layouts.admin')

@section('title', 'Log Audit CRUD')
@section('page_title', 'Log Audit CRUD (Khusus Superadmin)')

@section('content')
<div class="row">
    <div class="col-12">
        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <i class="bi bi-check-circle me-2"></i>{{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif
        @if(session('error'))
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <i class="bi bi-exclamation-triangle me-2"></i>{{ session('error') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        {{-- Filter Box --}}
        <div class="card mb-3 shadow-sm border-0">
            <div class="card-body">
                <form action="{{ route('admin.audit-logs.index') }}" method="GET" id="filterForm">
                    <div class="row g-2 align-items-end">
                        <div class="col-md-2">
                            <label class="form-label fs-7 fw-semibold text-muted">Aksi / Event</label>
                            <select name="event" class="form-select form-select-sm">
                                <option value="">Semua Aksi</option>
                                <option value="created" {{ request('event') == 'created' ? 'selected' : '' }}>Created (Dibuat)</option>
                                <option value="updated" {{ request('event') == 'updated' ? 'selected' : '' }}>Updated (Diubah)</option>
                                <option value="deleted" {{ request('event') == 'deleted' ? 'selected' : '' }}>Deleted (Dihapus)</option>
                            </select>
                        </div>
                        <div class="col-md-2">
                            <label class="form-label fs-7 fw-semibold text-muted">Modul / Data</label>
                            <select name="model" class="form-select form-select-sm">
                                <option value="">Semua Modul</option>
                                @foreach($availableModels as $class => $label)
                                    <option value="{{ $class }}" {{ request('model') == $class ? 'selected' : '' }}>{{ $label }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-2">
                            <label class="form-label fs-7 fw-semibold text-muted">Pelaku (User)</label>
                            <select name="user_id" class="form-select form-select-sm">
                                <option value="">Semua Pelaku</option>
                                @foreach($actors as $actor)
                                    <option value="{{ $actor->id }}" {{ request('user_id') == $actor->id ? 'selected' : '' }}>
                                        {{ $actor->name }} ({{ $actor->employee_id ?? 'No-ID' }})
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-2">
                            <label class="form-label fs-7 fw-semibold text-muted">Dari Tanggal</label>
                            <input type="date" name="date_from" class="form-control form-control-sm" value="{{ request('date_from') }}">
                        </div>
                        <div class="col-md-2">
                            <label class="form-label fs-7 fw-semibold text-muted">Sampai Tanggal</label>
                            <input type="date" name="date_to" class="form-control form-control-sm" value="{{ request('date_to') }}">
                        </div>
                        <div class="col-md-2 d-flex gap-1">
                            <button type="submit" class="btn btn-primary btn-sm w-100">
                                <i class="bi bi-filter me-1"></i> Filter
                            </button>
                            <a href="{{ route('admin.audit-logs.index') }}" class="btn btn-outline-secondary btn-sm" title="Reset Filter">
                                <i class="bi bi-arrow-counterclockwise"></i>
                            </a>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        {{-- Main Table Card --}}
        <div class="card shadow-sm border-0">
            <div class="card-header d-flex justify-content-between align-items-center flex-wrap gap-2 bg-white">
                <div class="d-flex align-items-center gap-2">
                    <h3 class="card-title fw-bold mb-0 text-dark">
                        <i class="bi bi-journal-check text-primary me-2"></i>Daftar Jejak Audit
                    </h3>
                    <span class="badge bg-light text-dark border ms-1">{{ $logs->total() }} Total Catatan</span>
                </div>

                <div class="d-flex align-items-center ms-auto gap-2">
                    <div class="d-flex align-items-center">
                        <span class="text-nowrap me-2 fs-7 text-muted">Baris:</span>
                        <select name="per_page" class="form-select form-select-sm" form="filterForm" onchange="this.form.submit()" style="width: 75px;">
                            <option value="10" {{ request('per_page') == 10 ? 'selected' : '' }}>10</option>
                            <option value="25" {{ request('per_page', 25) == 25 ? 'selected' : '' }}>25</option>
                            <option value="50" {{ request('per_page') == 50 ? 'selected' : '' }}>50</option>
                            <option value="100" {{ request('per_page') == 100 ? 'selected' : '' }}>100</option>
                        </select>
                    </div>

                    <div class="input-group input-group-sm" style="width: 220px;">
                        <input type="text" name="search" form="filterForm" class="form-control" placeholder="Cari IP, ID, Nama..." value="{{ request('search') }}">
                        <button type="submit" form="filterForm" class="btn btn-outline-secondary">
                            <i class="bi bi-search"></i>
                        </button>
                    </div>

                    @if(Route::has('admin.audit-logs.export'))
                        <button type="button" class="btn btn-success btn-sm text-nowrap" data-bs-toggle="modal" data-bs-target="#exportModal">
                            <i class="bi bi-file-earmark-excel me-1"></i> Export Excel
                        </button>
                    @endif

                    @if(Route::has('admin.audit-logs.archives.index'))
                        <a href="{{ route('admin.audit-logs.archives.index') }}" class="btn btn-danger btn-sm text-nowrap">
                            <i class="bi bi-shield-x me-1"></i> Kelola Arsip & Hapus
                        </a>
                    @endif
                </div>
            </div>

            <div class="card-body table-responsive p-0">
                <table class="table table-hover align-middle mb-0 text-nowrap">
                    <thead class="table-light">
                        <tr>
                            <th style="width: 170px;">Waktu Kejadian</th>
                            <th>Pelaku (User)</th>
                            <th style="width: 120px;">Aksi</th>
                            <th>Modul Data</th>
                            <th>ID Data</th>
                            <th>Alamat IP</th>
                            <th style="width: 100px;" class="text-center">Detail</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($logs as $log)
                        <tr>
                            <td class="text-muted small">
                                <i class="bi bi-clock me-1"></i>{{ $log->created_at?->format('d/m/Y H:i:s') ?? '-' }}
                            </td>
                            <td>
                                @if($log->user)
                                    <div class="d-flex align-items-center">
                                        <div class="avatar-circle-sm bg-primary text-white rounded-circle d-flex align-items-center justify-content-center me-2" style="width: 28px; height: 28px; font-size: 12px; font-weight: bold;">
                                            {{ strtoupper(substr($log->user->name, 0, 1)) }}
                                        </div>
                                        <div>
                                            <div class="fw-semibold text-dark">{{ $log->user->name }}</div>
                                            <div class="text-muted fs-8">{{ $log->user->employee_id ?? $log->user->email }}</div>
                                        </div>
                                    </div>
                                @else
                                    <span class="badge bg-secondary">Sistem / Otomatis</span>
                                @endif
                            </td>
                            <td>
                                {!! $log->event_badge !!}
                            </td>
                            <td>
                                <span class="fw-semibold text-dark">{{ $log->model_label }}</span>
                            </td>
                            <td>
                                <code class="text-primary fw-bold">#{{ $log->auditable_id }}</code>
                            </td>
                            <td>
                                <small class="text-muted font-monospace"><i class="bi bi-hdd-network me-1"></i>{{ $log->ip_address ?? '-' }}</small>
                            </td>
                            <td class="text-center">
                                <button type="button" class="btn btn-outline-primary btn-sm btn-view-detail"
                                    data-log="{{ json_encode([
                                        'id' => $log->id,
                                        'event' => $log->event,
                                        'event_badge' => $log->event_badge,
                                        'model_label' => $log->model_label,
                                        'auditable_type' => $log->auditable_type,
                                        'auditable_id' => $log->auditable_id,
                                        'actor' => $log->user ? $log->user->name . ' (' . ($log->user->employee_id ?? $log->user->email) . ')' : 'Sistem',
                                        'ip_address' => $log->ip_address ?? '-',
                                        'user_agent' => $log->user_agent ?? '-',
                                        'created_at' => $log->created_at?->format('d M Y, H:i:s') ?? '-',
                                        'before' => $log->before ?? [],
                                        'after' => $log->after ?? []
                                    ]) }}">
                                    <i class="bi bi-eye me-1"></i> Diff
                                </button>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="7" class="text-center py-5 text-muted">
                                <i class="bi bi-journal-x fs-1 d-block mb-2 text-secondary opacity-50"></i>
                                Tidak ada log aktivitas yang cocok dengan kriteria filter saat ini.
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if($logs->hasPages())
            <div class="card-footer bg-white border-top d-flex justify-content-between align-items-center py-2">
                <span class="text-muted small">
                    Menampilkan {{ $logs->firstItem() ?? 0 }} - {{ $logs->lastItem() ?? 0 }} dari {{ $logs->total() }} entri
                </span>
                <div>
                    {{ $logs->links() }}
                </div>
            </div>
            @endif
        </div>
    </div>
</div>

{{-- Modal Detail Perubahan (Diff) --}}
<div class="modal fade" id="auditDetailModal" tabindex="-1" aria-labelledby="auditDetailModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable">
        <div class="modal-content border-0 shadow">
            <div class="modal-header bg-light border-bottom">
                <h5 class="modal-title fw-bold" id="auditDetailModalLabel">
                    <i class="bi bi-file-diff me-2 text-primary"></i>Detail Perubahan Nilai (Diff)
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-4">
                {{-- Metadata Banner --}}
                <div class="p-3 mb-3 rounded border bg-light d-flex flex-wrap justify-content-between gap-3">
                    <div>
                        <span class="text-muted fs-8 text-uppercase fw-bold d-block">Modul & ID</span>
                        <span class="fw-bold fs-6 text-dark" id="modalModelLabel">-</span>
                    </div>
                    <div>
                        <span class="text-muted fs-8 text-uppercase fw-bold d-block">Tipe Aksi</span>
                        <span id="modalEventBadge">-</span>
                    </div>
                    <div>
                        <span class="text-muted fs-8 text-uppercase fw-bold d-block">Pelaku</span>
                        <span class="fw-semibold text-dark" id="modalActor">-</span>
                    </div>
                    <div>
                        <span class="text-muted fs-8 text-uppercase fw-bold d-block">Waktu & IP</span>
                        <span class="text-muted small" id="modalTimeIp">-</span>
                    </div>
                </div>

                <div class="mb-3">
                    <span class="text-muted fs-8 fw-bold text-uppercase d-block mb-1">User Agent (Browser)</span>
                    <code class="small text-secondary d-block p-2 rounded bg-light border" id="modalUserAgent" style="word-break: break-all;">-</code>
                </div>

                <h6 class="fw-bold mb-2 text-dark"><i class="bi bi-layout-split me-1"></i>Perbandingan Atribut:</h6>

                {{-- Diff Table --}}
                <div class="table-responsive border rounded">
                    <table class="table table-sm table-bordered mb-0 align-middle">
                        <thead class="table-light">
                            <tr>
                                <th style="width: 25%;">Nama Kolom</th>
                                <th style="width: 37.5%;" class="text-danger"><i class="bi bi-dash-circle me-1"></i>Sebelum (Before)</th>
                                <th style="width: 37.5%;" class="text-success"><i class="bi bi-plus-circle me-1"></i>Sesudah (After)</th>
                            </tr>
                        </thead>
                        <tbody id="modalDiffTableBody">
                            {{-- Rows populated via JavaScript --}}
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="modal-footer bg-light border-top">
                <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Tutup</button>
            </div>
        </div>
    </div>
</div>

{{-- Modal Export Excel dengan Preset & Rentang Tanggal --}}
<div class="modal fade" id="exportModal" tabindex="-1" aria-labelledby="exportModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow">
            <div class="modal-header bg-success text-white">
                <h5 class="modal-title fw-bold" id="exportModalLabel">
                    <i class="bi bi-file-earmark-excel me-2"></i>Export Log Audit ke Excel (.xlsx)
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ route('admin.audit-logs.export') }}" method="GET" id="exportForm">
                <div class="modal-body p-4">
                    {{-- Quick Presets --}}
                    <label class="form-label fs-7 fw-bold text-muted text-uppercase mb-2">
                        <i class="bi bi-lightning-charge-fill text-warning me-1"></i>Pilihan Cepat (Preset Waktu)
                    </label>
                    <div class="d-flex flex-wrap gap-1 mb-3">
                        <button type="button" class="btn btn-outline-primary btn-sm export-preset-btn" data-preset="today">Hari Ini</button>
                        <button type="button" class="btn btn-primary btn-sm export-preset-btn active" data-preset="last_7_days">7 Hari Terakhir</button>
                        <button type="button" class="btn btn-outline-primary btn-sm export-preset-btn" data-preset="last_30_days">30 Hari Terakhir</button>
                        <button type="button" class="btn btn-outline-primary btn-sm export-preset-btn" data-preset="this_month">Bulan Ini</button>
                        <button type="button" class="btn btn-outline-primary btn-sm export-preset-btn" data-preset="last_month">Bulan Lalu</button>
                        <button type="button" class="btn btn-outline-primary btn-sm export-preset-btn" data-preset="all">Semua Waktu</button>
                    </div>

                    {{-- Custom Date Range --}}
                    <div class="row g-2 mb-3">
                        <div class="col-6">
                            <label class="form-label fs-7 fw-semibold text-muted">Dari Tanggal (Inklusif)</label>
                            <input type="date" name="date_from" id="exportDateFrom" class="form-control form-control-sm" value="{{ request('date_from', now()->subDays(6)->toDateString()) }}">
                        </div>
                        <div class="col-6">
                            <label class="form-label fs-7 fw-semibold text-muted">Sampai Tanggal (Inklusif)</label>
                            <input type="date" name="date_to" id="exportDateTo" class="form-control form-control-sm" value="{{ request('date_to', now()->toDateString()) }}">
                        </div>
                    </div>

                    {{-- Filter Options --}}
                    <div class="row g-2 mb-3">
                        <div class="col-6">
                            <label class="form-label fs-7 fw-semibold text-muted">Aksi / Event</label>
                            <select name="event" class="form-select form-select-sm">
                                <option value="">Semua Aksi</option>
                                <option value="created" {{ request('event') == 'created' ? 'selected' : '' }}>Created (Dibuat)</option>
                                <option value="updated" {{ request('event') == 'updated' ? 'selected' : '' }}>Updated (Diubah)</option>
                                <option value="deleted" {{ request('event') == 'deleted' ? 'selected' : '' }}>Deleted (Dihapus)</option>
                            </select>
                        </div>
                        <div class="col-6">
                            <label class="form-label fs-7 fw-semibold text-muted">Modul Data</label>
                            <select name="model" class="form-select form-select-sm">
                                <option value="">Semua Modul</option>
                                @foreach($availableModels as $class => $label)
                                    <option value="{{ $class }}" {{ request('model') == $class ? 'selected' : '' }}>{{ $label }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fs-7 fw-semibold text-muted">Pelaku (User)</label>
                        <select name="user_id" class="form-select form-select-sm">
                            <option value="">Semua Pelaku</option>
                            @foreach($actors as $actor)
                                <option value="{{ $actor->id }}" {{ request('user_id') == $actor->id ? 'selected' : '' }}>
                                    {{ $actor->name }} ({{ $actor->employee_id ?? 'No-ID' }})
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <input type="hidden" name="search" value="{{ request('search') }}">

                    <div class="p-2 rounded bg-light border small text-muted">
                        <i class="bi bi-info-circle me-1 text-primary"></i> Berkas Excel (.xlsx) akan otomatis terunduh ke komputer Anda.
                    </div>
                </div>
                <div class="modal-footer bg-light border-top">
                    <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-success btn-sm">
                        <i class="bi bi-download me-1"></i> Download Excel (.xlsx)
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    const detailModalEl = document.getElementById('auditDetailModal');
    if (!detailModalEl) return;

    const detailModal = new bootstrap.Modal(detailModalEl);

    document.querySelectorAll('.btn-view-detail').forEach(button => {
        button.addEventListener('click', function () {
            const data = JSON.parse(this.dataset.log);

            document.getElementById('modalModelLabel').innerText = `${data.model_label} #${data.auditable_id}`;
            document.getElementById('modalEventBadge').innerHTML = data.event_badge;
            document.getElementById('modalActor').innerText = data.actor;
            document.getElementById('modalTimeIp').innerText = `${data.created_at} (${data.ip_address})`;
            document.getElementById('modalUserAgent').innerText = data.user_agent;

            const tbody = document.getElementById('modalDiffTableBody');
            tbody.innerHTML = '';

            let before = data.before || {};
            let after = data.after || {};

            while (typeof before === 'string') {
                try {
                    const parsed = JSON.parse(before);
                    before = parsed;
                } catch (e) { break; }
            }
            while (typeof after === 'string') {
                try {
                    const parsed = JSON.parse(after);
                    after = parsed;
                } catch (e) { break; }
            }

            if (!before || typeof before !== 'object' || Array.isArray(before)) before = {};
            if (!after || typeof after !== 'object' || Array.isArray(after)) after = {};

            const sensitiveKeys = ['password', 'remember_token', 'avatar_base64', 'selfie_base64'];
            sensitiveKeys.forEach(k => {
                delete before[k];
                delete after[k];
            });

            // Collect all unique keys
            const allKeys = Array.from(new Set([...Object.keys(before), ...Object.keys(after)]));

            if (allKeys.length === 0) {
                tbody.innerHTML = `<tr><td colspan="3" class="text-center text-muted py-3">Tidak ada atribut spesifik yang direkam.</td></tr>`;
            } else {
                allKeys.forEach(key => {
                    const beforeVal = before.hasOwnProperty(key) ? formatValue(before[key]) : '<span class="text-muted fst-italic">-</span>';
                    const afterVal = after.hasOwnProperty(key) ? formatValue(after[key]) : '<span class="text-muted fst-italic">-</span>';

                    const isChanged = before.hasOwnProperty(key) && after.hasOwnProperty(key) && JSON.stringify(before[key]) !== JSON.stringify(after[key]);

                    const row = document.createElement('tr');
                    row.innerHTML = `
                        <td class="fw-semibold text-secondary font-monospace small">${escapeHtml(key)}</td>
                        <td class="${isChanged ? 'bg-danger-subtle text-danger' : ''} small font-monospace">${beforeVal}</td>
                        <td class="${isChanged ? 'bg-success-subtle text-success' : ''} small font-monospace">${afterVal}</td>
                    `;
                    tbody.appendChild(row);
                });
            }

            detailModal.show();
        });
    });

    // Export Presets Handler
    const exportPresetButtons = document.querySelectorAll('.export-preset-btn');
    const exportDateFrom = document.getElementById('exportDateFrom');
    const exportDateTo = document.getElementById('exportDateTo');

    function formatDate(d) {
        const year = d.getFullYear();
        const month = String(d.getMonth() + 1).padStart(2, '0');
        const day = String(d.getDate()).padStart(2, '0');
        return `${year}-${month}-${day}`;
    }

    if (exportPresetButtons.length && exportDateFrom && exportDateTo) {
        exportPresetButtons.forEach(btn => {
            btn.addEventListener('click', function () {
                exportPresetButtons.forEach(b => {
                    b.classList.remove('btn-primary', 'active');
                    b.classList.add('btn-outline-primary');
                });
                this.classList.remove('btn-outline-primary');
                this.classList.add('btn-primary', 'active');

                const preset = this.dataset.preset;
                const now = new Date();

                if (preset === 'today') {
                    exportDateFrom.value = formatDate(now);
                    exportDateTo.value = formatDate(now);
                } else if (preset === 'last_7_days') {
                    const from = new Date();
                    from.setDate(now.getDate() - 6);
                    exportDateFrom.value = formatDate(from);
                    exportDateTo.value = formatDate(now);
                } else if (preset === 'last_30_days') {
                    const from = new Date();
                    from.setDate(now.getDate() - 29);
                    exportDateFrom.value = formatDate(from);
                    exportDateTo.value = formatDate(now);
                } else if (preset === 'this_month') {
                    const from = new Date(now.getFullYear(), now.getMonth(), 1);
                    const to = new Date(now.getFullYear(), now.getMonth() + 1, 0);
                    exportDateFrom.value = formatDate(from);
                    exportDateTo.value = formatDate(to);
                } else if (preset === 'last_month') {
                    const from = new Date(now.getFullYear(), now.getMonth() - 1, 1);
                    const to = new Date(now.getFullYear(), now.getMonth(), 0);
                    exportDateFrom.value = formatDate(from);
                    exportDateTo.value = formatDate(to);
                } else if (preset === 'all') {
                    exportDateFrom.value = '';
                    exportDateTo.value = '';
                }
            });
        });

        [exportDateFrom, exportDateTo].forEach(input => {
            input.addEventListener('change', function () {
                exportPresetButtons.forEach(b => {
                    b.classList.remove('btn-primary', 'active');
                    b.classList.add('btn-outline-primary');
                });
            });
        });
    }

    function formatValue(val) {
        if (val === null || val === undefined) return '<span class="text-muted fst-italic">null</span>';
        if (typeof val === 'boolean') return val ? '<span class="badge bg-success">true</span>' : '<span class="badge bg-secondary">false</span>';
        if (typeof val === 'object') return `<pre class="mb-0 small" style="white-space: pre-wrap;">${escapeHtml(JSON.stringify(val, null, 2))}</pre>`;
        return escapeHtml(String(val));
    }

    function escapeHtml(text) {
        const div = document.createElement('div');
        div.innerText = text;
        return div.innerHTML;
    }
});
</script>
@endpush
