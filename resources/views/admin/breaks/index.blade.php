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
                                <input type="date" name="date" class="form-control" title="Filter berdasarkan tanggal"
                                    value="{{ request('date') }}">
                                <input type="text" name="search" class="form-control" placeholder="Cari nama, NIK..."
                                    value="{{ request('search') }}">
                                <button type="submit" class="btn btn-default">
                                    <i class="bi bi-search"></i>
                                </button>
                            </div>
                        </form>
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
                                            <span class="text-warning fw-bold">{{ \Carbon\Carbon::parse($startBreak->timestamp)->format('H:i') }}</span>
                                        @else
                                            <span class="text-muted">-</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if ($endBreak)
                                            <span class="text-info fw-bold">{{ \Carbon\Carbon::parse($endBreak->timestamp)->format('H:i') }}</span>
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
                                        <span class="badge text-bg-{{ $attendance->break_status['color'] }}">
                                            {{ $attendance->break_status['label'] }}
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

@endsection
