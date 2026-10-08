@extends('layouts.admin')

@section('title', 'Riwayat Presensi')
@section('page_title', 'Riwayat Presensi')

@section('content')
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center flex-wrap gap-2">
                    <h3 class="card-title mb-0">Riwayat Kehadiran Karyawan</h3>
                    <div class="d-flex align-items-center ms-auto gap-2">
                        <form action="{{ route('admin.attendances.index') }}" method="GET" class="d-flex gap-2 align-items-center">
                            <div class="d-flex align-items-center text-nowrap">
                                <span class="me-2 small text-muted">Show</span>
                                <select name="per_page" class="form-select form-select-sm w-auto" onchange="this.form.submit()">
                                    <option value="10" {{ request('per_page') == 10 ? 'selected' : '' }}>10</option>
                                    <option value="25" {{ request('per_page') == 25 ? 'selected' : '' }}>25</option>
                                    <option value="50" {{ request('per_page') == 50 ? 'selected' : '' }}>50</option>
                                    <option value="100" {{ request('per_page') == 100 ? 'selected' : '' }}>100</option>
                                </select>
                            </div>
                            <div class="input-group input-group-sm">
                                <input type="date" name="date" class="form-control" title="Filter berdasarkan tanggal"
                                    value="{{ request('date') }}">
                                <input type="text" name="search" class="form-control" placeholder="Cari nama, NIK..."
                                    value="{{ request('search') }}">
                                <button type="submit" class="btn btn-default">
                                    <i class="bi bi-search"></i>
                                </button>
                            </div>
                        </form>
                        <button type="button" class="btn btn-success btn-sm text-nowrap" data-bs-toggle="modal"
                            data-bs-target="#exportModal">
                            <i class="bi bi-file-earmark-excel"></i> Export Excel
                        </button>
                    </div>
                </div>
                <div class="card-body table-responsive p-0">
                    <table class="table table-hover text-nowrap">
                        <thead>
                            <tr>
                                <th>Tanggal</th>
                                <th>Karyawan</th>
                                <th>Lokasi Kerja</th>
                                <th>Shift</th>

                                <th>Masuk Aktual</th>
                                <th>Keluar Aktual</th>
                                <th>Foto</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($attendances as $attendance)
                                @php
                                    $checkIn = $attendance->events->where('event_type', \App\Enums\EventType::START_SHIFT)->first();
                                    $checkOut = $attendance->events->where('event_type', \App\Enums\EventType::END_SHIFT)->first();
                                @endphp
                                <tr>
                                    <td>{{ $attendance->date ? $attendance->date->format('d M Y') : '-' }}</td>
                                    <td>
                                        <div>{{ $attendance->user->name ?? '-' }}</div>
                                        <div class="text-muted small">{{ $attendance->user->nik ?? '-' }}</div>
                                    </td>
                                    <td>{{ $attendance->user->location->name ?? '-' }}</td>
                                    <td>{{ $attendance->shift->shift_name ?? '-' }}</td>

                                    <td>
                                        @if($checkIn)
                                            @php
                                                $scheduledStart = $attendance->shift->default_start_time ? \Carbon\Carbon::parse($attendance->shift->default_start_time) : null;
                                                $actualStart = $checkIn->timestamp;
                                                $isLate = $scheduledStart && $actualStart->format('H:i') > $scheduledStart->format('H:i');
                                            @endphp
                                            <span class="{{ $isLate ? 'text-danger fw-bold' : 'text-success' }}">{{ $actualStart->format('H:i') }}</span>
                                        @else
                                            <span class="text-muted">-</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if($checkOut)
                                            @php
                                                $scheduledEnd = $attendance->shift->default_end_time ? \Carbon\Carbon::parse($attendance->shift->default_end_time) : null;
                                                $actualEnd = $checkOut->timestamp;
                                                $isEarly = $scheduledEnd && $actualEnd->format('H:i') < $scheduledEnd->format('H:i');
                                            @endphp
                                            <span class="{{ $isEarly ? 'text-warning fw-bold' : 'text-success' }}">{{ $actualEnd->format('H:i') }}</span>
                                        @else
                                            <span class="text-muted">-</span>
                                        @endif
                                    </td>
                                    <td>
                                        <div class="d-flex gap-1">
                                            @if($checkIn && $checkIn->selfie_path && Storage::disk('public')->exists($checkIn->selfie_path))
                                                <a href="javascript:void(0)" onclick="showLightbox('{{ Storage::url($checkIn->selfie_path) }}')" title="Foto Masuk">
                                                    <img src="{{ Storage::url($checkIn->selfie_path) }}" alt="In" class="img-thumbnail p-1" style="width: 40px; height: 40px; object-fit: cover;">
                                                </a>
                                            @else
                                                <a href="javascript:void(0)" onclick="showLightbox('{{ asset('placeholder.svg') }}')" title="Tidak ada foto masuk">
                                                    <img src="{{ asset('placeholder.svg') }}" alt="In" class="img-thumbnail p-1" style="width: 40px; height: 40px; object-fit: cover;">
                                                </a>
                                            @endif
                                            @if($checkOut && $checkOut->selfie_path && Storage::disk('public')->exists($checkOut->selfie_path))
                                                <a href="javascript:void(0)" onclick="showLightbox('{{ Storage::url($checkOut->selfie_path) }}')" title="Foto Keluar">
                                                    <img src="{{ Storage::url($checkOut->selfie_path) }}" alt="Out" class="img-thumbnail p-1" style="width: 40px; height: 40px; object-fit: cover;">
                                                </a>
                                            @else
                                                <a href="javascript:void(0)" onclick="showLightbox('{{ asset('placeholder.svg') }}')" title="Tidak ada foto keluar">
                                                    <img src="{{ asset('placeholder.svg') }}" alt="Out" class="img-thumbnail p-1" style="width: 40px; height: 40px; object-fit: cover;">
                                                </a>
                                            @endif
                                        </div>
                                    </td>
                                    <td>
                                        <span class="badge text-bg-{{ $attendance->status->color() ?? 'secondary' }}">
                                            {{ $attendance->status->label() ?? $attendance->status->value }}
                                        </span>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="8" class="text-center">Belum ada data presensi.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                <div class="card-footer clearfix">
                    {{ $attendances->links() }}
                </div>
            </div>
        </div>
    </div>

    <!-- Modal Export -->
    <div class="modal fade" id="exportModal" tabindex="-1" aria-labelledby="exportModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <form action="{{ route('admin.attendances.export') }}" method="GET">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="exportModalLabel">Export Data Presensi</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label">Pilih Rentang Waktu</label>
                            <select class="form-select" name="export_type" id="export_type" onchange="toggleCustomDate()">
                                <option value="today">Hari Ini</option>
                                <option value="all">Semua Hari</option>
                                <option value="custom">Ditentukan (Custom)</option>
                            </select>
                        </div>
                        <div class="row" id="custom_date_container" style="display: none;">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Mulai Tanggal</label>
                                <input type="date" name="start_date" class="form-control">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Sampai Tanggal</label>
                                <input type="date" name="end_date" class="form-control">
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-success"><i class="bi bi-download"></i> Download Excel</button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Modal Lightbox -->
    <div class="modal fade" id="lightboxModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content bg-transparent border-0">
                <div class="modal-header border-0 pb-0 justify-content-end">
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body text-center pt-0">
                    <img id="lightboxImage" src="" class="img-fluid rounded" alt="Selfie" style="max-height: 80vh;">
                </div>
            </div>
        </div>
    </div>

    <script>
        function showLightbox(imageUrl) {
            document.getElementById('lightboxImage').src = imageUrl;
            var lightboxModal = new bootstrap.Modal(document.getElementById('lightboxModal'));
            lightboxModal.show();
        }

        function toggleCustomDate() {
            var type = document.getElementById('export_type').value;
            var container = document.getElementById('custom_date_container');
            if (type === 'custom') {
                container.style.display = 'flex';
            } else {
                container.style.display = 'none';
            }
        }
    </script>
@endsection