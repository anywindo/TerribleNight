@extends('layouts.admin')

@section('title', 'Riwayat Istirahat')
@section('page_title', 'Riwayat Istirahat')

@section('content')
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center flex-wrap gap-2">
                    <h3 class="card-title mb-0">Riwayat Istirahat Karyawan</h3>
                    <div class="d-flex align-items-center ms-auto gap-2">
                        <form action="{{ route('admin.breaks.index') }}" method="GET" class="d-flex gap-2 align-items-center">
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
                                <select name="location_id" class="form-select" onchange="this.form.submit()">
                                    <option value="">Semua Lokasi</option>
                                    @foreach($locations ?? [] as $loc)
                                        <option value="{{ $loc->id }}" {{ request('location_id') == $loc->id ? 'selected' : '' }}>{{ $loc->name }}</option>
                                    @endforeach
                                </select>
                                
                                <select name="shift_id" class="form-select" onchange="this.form.submit()">
                                    <option value="">Semua Shift</option>
                                    @foreach($shifts ?? [] as $shift)
                                        <option value="{{ $shift->id }}" {{ request('shift_id') == $shift->id ? 'selected' : '' }}>{{ $shift->shift_name }}</option>
                                    @endforeach
                                </select>

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
                                <th>Mulai Istirahat</th>
                                <th>Selesai Istirahat</th>
                                <th>Durasi Istirahat</th>
                                <th>Foto</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($attendances as $attendance)
                                @php
                                    $startBreak = $attendance->events->where('event_type', 'START_BREAK')->first();
                                    $endBreak = $attendance->events->where('event_type', 'END_BREAK')->first();
                                    
                                    $duration = '-';
                                    if ($startBreak && $endBreak) {
                                        $diff = \Carbon\Carbon::parse($endBreak->timestamp)->diff(\Carbon\Carbon::parse($startBreak->timestamp));
                                        $duration = sprintf('%02d:%02d:%02d', $diff->h, $diff->i, $diff->s);
                                    }

                                    // Custom Status Logic for Admin Panel
                                    $breakStatusLabel = '-';
                                    $breakStatusColor = 'secondary';
                                    $startColorClass = 'text-muted';
                                    $endColorClass = 'text-muted';

                                    if ($startBreak && $attendance->shift && $attendance->shift->break_start && $attendance->shift->break_end) {
                                        $startColorClass = 'text-warning'; // Default
                                        $endColorClass = 'text-info'; // Default

                                        if (!$endBreak) {
                                            $breakStatusLabel = 'Sedang Istirahat';
                                            $breakStatusColor = 'warning';
                                        } else {
                                            $actualStartTime = \Carbon\Carbon::parse($startBreak->timestamp)->format('H:i:s');
                                            $expectedStartTime = \Carbon\Carbon::parse($attendance->shift->break_start)->format('H:i:s');
                                            
                                            $actualEndTime = \Carbon\Carbon::parse($endBreak->timestamp)->format('H:i:s');
                                            $expectedEndTime = \Carbon\Carbon::parse($attendance->shift->break_end)->format('H:i:s');

                                            $isEarlyBreak = ($actualStartTime < $expectedStartTime);
                                            $isLateBreak = ($actualEndTime > $expectedEndTime);

                                            // Coloring the time texts
                                            $startColorClass = $isEarlyBreak ? 'text-danger' : 'text-success';
                                            $endColorClass = $isLateBreak ? 'text-danger' : 'text-success';

                                            // Badge Status
                                            if ($isLateBreak) {
                                                $breakStatusLabel = 'Terlambat';
                                                $breakStatusColor = 'danger';
                                            } else {
                                                $breakStatusLabel = 'Tepat Waktu';
                                                $breakStatusColor = 'success';
                                            }

                                            if ($isEarlyBreak) {
                                                $breakStatusLabel = $isLateBreak ? 'Terlambat, Istirahat Awal' : 'Istirahat Awal';
                                                $breakStatusColor = 'danger';
                                            }
                                        }
                                    }
                                @endphp
                                <tr>
                                    <td>{{ \Carbon\Carbon::parse($attendance->date)->format('d/m/Y') }}</td>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <div>
                                                <strong>{{ $attendance->user->name }}</strong><br>
                                                <small class="text-muted">{{ $attendance->user->nik }}</small>
                                            </div>
                                        </div>
                                    </td>
                                    <td>{{ $attendance->user->location->name ?? '-' }}</td>
                                    <td>{{ $attendance->shift->shift_name ?? '-' }}</td>
                                    <td>
                                        @if ($startBreak)
                                            <span class="{{ $startColorClass }} fw-bold">{{ \Carbon\Carbon::parse($startBreak->timestamp)->format('H:i') }}</span>
                                        @else
                                            <span class="text-muted">-</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if ($endBreak)
                                            <span class="{{ $endColorClass }} fw-bold">{{ \Carbon\Carbon::parse($endBreak->timestamp)->format('H:i') }}</span>
                                        @else
                                            <span class="text-muted">-</span>
                                        @endif
                                    </td>
                                    <td>
                                        <span class="badge bg-secondary">{{ $duration }}</span>
                                    </td>
                                    <td>
                                        <div class="d-flex gap-1">
                                            @if($startBreak && $startBreak->selfie_path && Storage::disk('public')->exists($startBreak->selfie_path))
                                                <a href="javascript:void(0)" onclick="showLightbox('{{ Storage::url($startBreak->selfie_path) }}')" title="Foto Mulai Istirahat">
                                                    <img src="{{ Storage::url($startBreak->selfie_path) }}" alt="Mulai Istirahat" class="img-thumbnail p-1" style="width: 40px; height: 40px; object-fit: cover;">
                                                </a>
                                            @else
                                                <a href="javascript:void(0)" onclick="showLightbox('{{ asset('placeholder.svg') }}')" title="Tidak ada foto">
                                                    <img src="{{ asset('placeholder.svg') }}" alt="No Photo" class="img-thumbnail p-1" style="width: 40px; height: 40px; object-fit: cover;">
                                                </a>
                                            @endif
                                            @if($endBreak && $endBreak->selfie_path && Storage::disk('public')->exists($endBreak->selfie_path))
                                                <a href="javascript:void(0)" onclick="showLightbox('{{ Storage::url($endBreak->selfie_path) }}')" title="Foto Selesai Istirahat">
                                                    <img src="{{ Storage::url($endBreak->selfie_path) }}" alt="Selesai Istirahat" class="img-thumbnail p-1" style="width: 40px; height: 40px; object-fit: cover;">
                                                </a>
                                            @else
                                                <a href="javascript:void(0)" onclick="showLightbox('{{ asset('placeholder.svg') }}')" title="Tidak ada foto">
                                                    <img src="{{ asset('placeholder.svg') }}" alt="No Photo" class="img-thumbnail p-1" style="width: 40px; height: 40px; object-fit: cover;">
                                                </a>
                                            @endif
                                        </div>
                                    </td>
                                    <td>
                                        <span class="badge text-bg-{{ $breakStatusColor }}">
                                            {{ $breakStatusLabel }}
                                        </span>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="9" class="text-center py-4">Belum ada riwayat istirahat.</td>
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
            <form action="{{ route('admin.breaks.export') }}" method="GET">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="exportModalLabel">Export Data Istirahat</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label">Pilih Rentang Waktu</label>
                            <select class="form-select" name="export_type" id="export_type" onchange="toggleCustomDate()">
                                <option value="today">Hari Ini</option>
                                <option value="filter">Sesuai Filter Saat Ini</option>
                                <option value="all">Semua Hari</option>
                                <option value="custom">Ditentukan (Custom)</option>
                            </select>
                        </div>
                        
                        <!-- Hidden inputs for filter -->
                        <input type="hidden" name="search" value="{{ request('search') }}">
                        <input type="hidden" name="date" value="{{ request('date') }}">
                        <input type="hidden" name="shift_id" value="{{ request('shift_id') }}">
                        <input type="hidden" name="location_id" value="{{ request('location_id') }}">

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

    <script>
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
