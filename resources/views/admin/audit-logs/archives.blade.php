@extends('layouts.admin')

@section('title', 'Kelola Arsip & Pembersihan Log')
@section('page_title', 'Kelola Arsip & Pembersihan Log (Dual-Gate Security)')

@section('content')
<div class="row">
    <div class="col-12">
        <div class="d-flex align-items-center justify-content-between mb-3 flex-wrap gap-2">
            <a href="{{ route('admin.audit-logs.index') }}" class="btn btn-outline-secondary btn-sm">
                <i class="bi bi-arrow-left me-1"></i> Kembali ke Daftar Log
            </a>
            <span class="badge bg-primary fs-7">
                <i class="bi bi-database me-1"></i> Total Log Aktif Saat Ini: {{ number_format($totalLogs) }}
            </span>
        </div>

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

        {{-- Security Gate Policy Banner --}}
        <div class="alert alert-warning border-start border-4 border-warning shadow-sm">
            <div class="d-flex align-items-center mb-1">
                <i class="bi bi-shield-lock-fill fs-5 me-2 text-warning"></i>
                <h6 class="fw-bold mb-0 text-dark">Prosedur Keamanan Pembersihan Log (Dual-Gate Security)</h6>
            </div>
            <p class="mb-2 fs-7 text-dark">
                Demi menjaga integritas dan akuntabilitas sistem, penghapusan data log dilindungi oleh dua lapis pengamanan:
            </p>
            <ol class="mb-0 fs-7 text-dark ps-3">
                <li><strong>Gerbang 1 (Wajib Download Arsip):</strong> Berkas arsip Excel (.xlsx) harus dibuat dan berhasil diunduh ke komputer Anda sebagai berkas cadangan offline sebelum tombol hapus aktif.</li>
                <li><strong>Gerbang 2 (Verifikasi OTP Email):</strong> Penghapusan fisik data dari database hanya dapat dieksekusi setelah memasukkan kode OTP 6-digit yang dikirimkan ke email terdaftar Superadmin yang sedang login.</li>
            </ol>
        </div>

        {{-- Form Create Archive Card --}}
        <div class="card mb-4 shadow-sm border-0">
            <div class="card-header bg-white py-3">
                <h5 class="card-title fw-bold mb-0 text-dark">
                    <i class="bi bi-archive text-primary me-2"></i>Buat Berkas Arsip Baru
                </h5>
            </div>
            <div class="card-body">
                <form action="{{ route('admin.audit-logs.archives.store') }}" method="POST">
                    @csrf
                    <div class="row g-3 align-items-end">
                        <div class="col-md-4">
                            <label class="form-label fs-7 fw-semibold text-muted">Dari Tanggal (Inklusif)</label>
                            <input type="date" name="date_from" class="form-control @error('date_from') is-invalid @enderror" value="{{ old('date_from') }}" required>
                            @error('date_from')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fs-7 fw-semibold text-muted">Sampai Tanggal (Inklusif)</label>
                            <input type="date" name="date_to" class="form-control @error('date_to') is-invalid @enderror" value="{{ old('date_to') }}" required>
                            @error('date_to')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-md-4">
                            <button type="submit" class="btn btn-primary w-100">
                                <i class="bi bi-file-earmark-plus me-1"></i> Buat Berkas Arsip (.xlsx)
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        {{-- Archives Table Card --}}
        <div class="card shadow-sm border-0">
            <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center flex-wrap">
                <h5 class="card-title fw-bold mb-0 text-dark">
                    <i class="bi bi-folder-check text-success me-2"></i>Riwayat Berkas Arsip & Aksi Pembersihan
                </h5>
                <span class="badge bg-light text-dark border">{{ $archives->total() }} Total Arsip</span>
            </div>
            <div class="card-body table-responsive p-0">
                <table class="table table-hover align-middle mb-0 text-nowrap">
                    <thead class="table-light">
                        <tr>
                            <th>No. Arsip</th>
                            <th>Rentang Data</th>
                            <th>Total Baris</th>
                            <th>Dibuat Oleh</th>
                            <th>Status Download (Gerbang 1)</th>
                            <th>Status Pembersihan (Gerbang 2)</th>
                            <th class="text-center" style="width: 200px;">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($archives as $archive)
                        <tr id="archive-row-{{ $archive->id }}"
                            data-archive-id="{{ $archive->id }}"
                            data-range="{{ $archive->date_from->format('d/m/Y') }} s/d {{ $archive->date_to->format('d/m/Y') }}"
                            data-records="{{ number_format($archive->total_records) }}"
                            data-otp-url="{{ route('admin.audit-logs.archives.request-otp', $archive) }}"
                            data-purge-url="{{ route('admin.audit-logs.archives.purge', $archive) }}"
                            data-purged="{{ $archive->isPurged() ? 'true' : 'false' }}">
                            <td>
                                <span class="fw-bold text-dark">#ARSIP-{{ $archive->id }}</span>
                                <small class="d-block text-muted">{{ $archive->created_at->format('d/m/Y H:i') }}</small>
                            </td>
                            <td>
                                <span class="badge bg-secondary-subtle text-secondary border">
                                    <i class="bi bi-calendar-range me-1"></i>
                                    {{ $archive->date_from->format('d M Y') }} - {{ $archive->date_to->format('d M Y') }}
                                </span>
                            </td>
                            <td>
                                <span class="badge bg-info text-dark fw-bold">
                                    {{ number_format($archive->total_records) }} baris
                                </span>
                            </td>
                            <td>
                                <div class="fw-semibold text-dark">{{ $archive->user->name ?? 'Superadmin' }}</div>
                                <div class="text-muted fs-8">{{ $archive->user->email ?? '-' }}</div>
                            </td>
                            <td class="download-status-cell">
                                @if($archive->isDownloaded())
                                    <span class="badge bg-success">
                                        <i class="bi bi-check-circle me-1"></i> Diunduh
                                    </span>
                                    <small class="d-block text-muted fs-8 mt-1">
                                        {{ $archive->downloaded_at->format('d/m/Y H:i') }}
                                    </small>
                                @else
                                    <span class="badge bg-warning text-dark">
                                        <i class="bi bi-exclamation-circle me-1"></i> Belum Diunduh
                                    </span>
                                @endif
                            </td>
                            <td>
                                @if($archive->isPurged())
                                    <span class="badge bg-danger">
                                        <i class="bi bi-trash-fill me-1"></i> Sudah Dihapus
                                    </span>
                                    <small class="d-block text-muted fs-8 mt-1">
                                        {{ $archive->purged_at->format('d/m/Y H:i') }}
                                    </small>
                                @else
                                    <span class="badge bg-secondary">
                                        <i class="bi bi-shield-check me-1"></i> Data Masih Ada
                                    </span>
                                @endif
                            </td>
                            <td class="text-center">
                                <div class="d-flex justify-content-center gap-1">
                                    {{-- Download Button --}}
                                    <a href="{{ route('admin.audit-logs.archives.download', $archive) }}"
                                        class="btn btn-outline-success btn-sm btn-download-archive"
                                        title="Unduh Berkas Excel Cadangan">
                                        <i class="bi bi-download me-1"></i> Download
                                    </a>

                                    {{-- Purge Button Container --}}
                                    <span class="purge-btn-cell">
                                        @if($archive->isPurged())
                                            <button class="btn btn-secondary btn-sm" disabled title="Data log sudah dibersihkan">
                                                <i class="bi bi-check-all me-1"></i> Selesai
                                            </button>
                                        @elseif(!$archive->isDownloaded())
                                            <button class="btn btn-outline-danger btn-sm" disabled title="Wajib unduh berkas arsip terlebih dahulu!">
                                                <i class="bi bi-lock-fill me-1"></i> Hapus
                                            </button>
                                        @else
                                            <button type="button" class="btn btn-danger btn-sm btn-open-otp"
                                                data-archive-id="{{ $archive->id }}"
                                                data-range="{{ $archive->date_from->format('d/m/Y') }} s/d {{ $archive->date_to->format('d/m/Y') }}"
                                                data-records="{{ number_format($archive->total_records) }}"
                                                data-otp-url="{{ route('admin.audit-logs.archives.request-otp', $archive) }}"
                                                data-purge-url="{{ route('admin.audit-logs.archives.purge', $archive) }}">
                                                <i class="bi bi-trash me-1"></i> Hapus Log
                                            </button>
                                        @endif
                                    </span>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="7" class="text-center py-5 text-muted">
                                <i class="bi bi-folder-x fs-1 d-block mb-2 text-secondary opacity-50"></i>
                                Belum ada berkas arsip log yang dibuat. Gunakan form di atas untuk membuat arsip baru.
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            @if($archives->hasPages())
                <div class="card-footer bg-white border-top py-2">
                    {{ $archives->links() }}
                </div>
            @endif
        </div>
    </div>
</div>

{{-- Modal Konfirmasi OTP Pembersihan --}}
<div class="modal fade" id="purgeOtpModal" tabindex="-1" aria-labelledby="purgeOtpModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow">
            <div class="modal-header bg-danger text-white">
                <h5 class="modal-title fw-bold" id="purgeOtpModalLabel">
                    <i class="bi bi-shield-exclamation me-2"></i>Konfirmasi Pembersihan Log via OTP
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="purgeForm" method="POST" action="">
                @csrf
                <div class="modal-body p-4">
                    <div class="alert alert-danger bg-danger-subtle border-danger text-danger mb-3 fs-7">
                        <i class="bi bi-exclamation-triangle-fill me-1"></i>
                        <strong>PERHATIAN:</strong> Tindakan ini akan <u>menghapus secara permanen</u> data log riwayat di database. Pastikan berkas cadangan telah tersimpan dengan aman di komputer Anda.
                    </div>

                    <div class="p-3 bg-light rounded border mb-3">
                        <div class="d-flex justify-content-between mb-1">
                            <span class="text-muted fs-8 text-uppercase fw-bold">Nomor Arsip:</span>
                            <span class="fw-bold" id="modalArchiveNumber">-</span>
                        </div>
                        <div class="d-flex justify-content-between mb-1">
                            <span class="text-muted fs-8 text-uppercase fw-bold">Rentang Tanggal:</span>
                            <span class="fw-semibold text-dark" id="modalArchiveRange">-</span>
                        </div>
                        <div class="d-flex justify-content-between">
                            <span class="text-muted fs-8 text-uppercase fw-bold">Jumlah Data yang Dihapus:</span>
                            <span class="badge bg-danger" id="modalArchiveRecords">-</span>
                        </div>
                    </div>

                    <div class="mb-3">
                        <div class="d-flex justify-content-between align-items-center mb-1">
                            <label class="form-label fw-bold mb-0">Kode Verifikasi OTP (6 Digit)</label>
                            <button type="button" class="btn btn-link btn-sm p-0 text-decoration-none" id="btnRequestOtp">
                                <i class="bi bi-envelope me-1"></i> Kirim Kode ke Email
                            </button>
                        </div>
                        <input type="text" name="otp" id="otpInput" class="form-control text-center font-monospace fs-4 fw-bold letter-spacing-2" maxlength="6" placeholder="000000" required>
                        <div class="form-text fs-8 text-muted" id="otpHelpText">
                            Klik tombol "Kirim Kode ke Email" untuk menerima kode OTP ke <strong>{{ auth()->user()->email }}</strong>.
                        </div>
                    </div>
                </div>
                <div class="modal-footer bg-light border-top">
                    <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-danger btn-sm" id="btnConfirmPurge" disabled>
                        <i class="bi bi-trash-fill me-1"></i> Hapus Permanen dari Database
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
    const modalEl = document.getElementById('purgeOtpModal');
    if (!modalEl) return;

    const purgeModal = new bootstrap.Modal(modalEl);
    let activeOtpUrl = '';

    // Handle opening OTP modal using event delegation
    document.addEventListener('click', function (e) {
        const button = e.target.closest('.btn-open-otp');
        if (!button) return;

        const archiveId = button.dataset.archiveId;
        const range = button.dataset.range;
        const records = button.dataset.records;
        const otpUrl = button.dataset.otpUrl;
        const purgeUrl = button.dataset.purgeUrl;

        document.getElementById('modalArchiveNumber').innerText = `#ARSIP-${archiveId}`;
        document.getElementById('modalArchiveRange').innerText = range;
        document.getElementById('modalArchiveRecords').innerText = `${records} baris data`;
        document.getElementById('purgeForm').action = purgeUrl;

        activeOtpUrl = otpUrl;
        document.getElementById('otpInput').value = '';
        document.getElementById('btnConfirmPurge').disabled = true;
        document.getElementById('otpHelpText').innerHTML = `Klik tombol "Kirim Kode ke Email" untuk menerima kode OTP ke <strong>{{ auth()->user()->email }}</strong>.`;

        purgeModal.show();
    });

    // Handle instant unlock of Hapus button when Download is clicked
    document.querySelectorAll('.btn-download-archive').forEach(downloadBtn => {
        downloadBtn.addEventListener('click', function () {
            const row = this.closest('tr');
            if (!row) return;

            const isPurged = row.dataset.purged === 'true';
            const archiveId = row.dataset.archiveId;
            const range = row.dataset.range;
            const records = row.dataset.records;
            const otpUrl = row.dataset.otpUrl;
            const purgeUrl = row.dataset.purgeUrl;

            // Update Status Download cell immediately
            const statusCell = row.querySelector('.download-status-cell');
            if (statusCell) {
                statusCell.innerHTML = `
                    <span class="badge bg-success">
                        <i class="bi bi-check-circle me-1"></i> Diunduh
                    </span>
                    <small class="d-block text-muted fs-8 mt-1">Baru saja</small>
                `;
            }

            // Unlock Hapus button if not yet purged
            if (!isPurged) {
                const purgeCell = row.querySelector('.purge-btn-cell');
                if (purgeCell) {
                    purgeCell.innerHTML = `
                        <button type="button" class="btn btn-danger btn-sm btn-open-otp"
                            data-archive-id="${archiveId}"
                            data-range="${range}"
                            data-records="${records}"
                            data-otp-url="${otpUrl}"
                            data-purge-url="${purgeUrl}">
                            <i class="bi bi-trash me-1"></i> Hapus Log
                        </button>
                    `;
                }
            }
        });
    });

    const btnRequestOtp = document.getElementById('btnRequestOtp');
    btnRequestOtp.addEventListener('click', function () {
        if (!activeOtpUrl) return;

        btnRequestOtp.disabled = true;
        btnRequestOtp.innerHTML = `<span class="spinner-border spinner-border-sm me-1" role="status"></span> Mengirim...`;

        fetch(activeOtpUrl, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Accept': 'application/json',
                'Content-Type': 'application/json'
            }
        })
        .then(async res => {
            const data = await res.json().catch(() => null);
            if (!res.ok) {
                const errMsg = data?.message || `Server error (${res.status})`;
                throw new Error(errMsg);
            }
            return data;
        })
        .then(data => {
            if (data && data.success) {
                let msgHtml = `<span class="text-success fw-bold"><i class="bi bi-check-circle me-1"></i>${data.message}</span>`;
                if (data.dev_otp) {
                    msgHtml += `<div class="alert alert-info py-2 px-3 mt-2 mb-0 small"><strong>Mode Dev / Email Uji Coba:</strong> Gunakan kode OTP: <span class="badge bg-primary fs-6 font-monospace">${data.dev_otp}</span></div>`;
                    document.getElementById('otpInput').value = data.dev_otp;
                    document.getElementById('btnConfirmPurge').disabled = false;
                }
                document.getElementById('otpHelpText').innerHTML = msgHtml;
                btnRequestOtp.innerHTML = `<i class="bi bi-arrow-repeat me-1"></i> Kirim Ulang OTP`;
                startCountdown(60);
            } else {
                alert(data?.message || 'Gagal mengirim kode OTP.');
                btnRequestOtp.disabled = false;
                btnRequestOtp.innerHTML = `<i class="bi bi-envelope me-1"></i> Kirim Kode ke Email`;
            }
        })
        .catch(err => {
            alert(err.message || 'Terjadi kesalahan saat meminta OTP.');
            btnRequestOtp.disabled = false;
            btnRequestOtp.innerHTML = `<i class="bi bi-envelope me-1"></i> Kirim Kode ke Email`;
        });
    });

    const otpInput = document.getElementById('otpInput');
    otpInput.addEventListener('input', function () {
        this.value = this.value.replace(/[^0-9]/g, '');
        document.getElementById('btnConfirmPurge').disabled = (this.value.length !== 6);
    });

    function startCountdown(seconds) {
        let left = seconds;
        btnRequestOtp.disabled = true;
        const interval = setInterval(() => {
            left--;
            if (left <= 0) {
                clearInterval(interval);
                btnRequestOtp.disabled = false;
                btnRequestOtp.innerHTML = `<i class="bi bi-arrow-repeat me-1"></i> Kirim Ulang OTP`;
            } else {
                btnRequestOtp.innerHTML = `<i class="bi bi-clock me-1"></i> Tunggu ${left}s`;
            }
        }, 1000);
    }
});
</script>
@endpush
