@extends('layouts.admin')

@section('title', 'Dashboard HR')
@section('page_title', 'Dashboard')

@section('content')
    <div class="row">
        <div class="col-lg-4 col-12">
            <div class="small-box text-bg-primary">
                <div class="inner">
                    <h3>{{ $totalEmployees }}</h3>
                    <p>Total Karyawan</p>
                </div>
                <div class="small-box-icon">
                    <i class="bi bi-people-fill"></i>
                </div>
                <a href="{{ route('admin.employees.index') }}"
                    class="small-box-footer link-light link-underline-opacity-0 link-underline-opacity-50-hover">
                    Selengkapnya <i class="bi bi-link-45deg"></i>
                </a>
            </div>
        </div>

        <div class="col-lg-4 col-12">
            <div class="small-box text-bg-success">
                <div class="inner">
                    <h3>{{ $presentToday }}</h3>
                    <p>Hadir Hari Ini</p>
                </div>
                <div class="small-box-icon">
                    <i class="bi bi-person-check-fill"></i>
                </div>
                <a href="{{ route('admin.attendances.index', ['date' => now()->format('Y-m-d')]) }}"
                    class="small-box-footer link-light link-underline-opacity-0 link-underline-opacity-50-hover">
                    Selengkapnya <i class="bi bi-link-45deg"></i>
                </a>
            </div>
        </div>



        <div class="col-lg-4 col-12">
            <div class="small-box text-bg-danger">
                <div class="inner">
                    <h3>{{ $absentToday }}</h3>
                    <p>Karyawan Absen</p>
                </div>
                <div class="small-box-icon">
                    <i class="bi bi-person-dash-fill"></i>
                </div>
                <a href="#" class="small-box-footer link-light link-underline-opacity-0 link-underline-opacity-50-hover">
                    Selengkapnya <i class="bi bi-link-45deg"></i>
                </a>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-12">
            <div class="card mb-4">
                <div class="card-header">
                    <h3 class="card-title">Selamat datang di HR Portal</h3>
                </div>
                <div class="card-body">
                    <p>Gunakan menu di sebelah kiri untuk mengelola master data karyawan dan melihat riwayat presensi.</p>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-12">
            <div class="card mb-4">
                <div class="card-header text-bg-warning">
                    <h3 class="card-title"><i class="bi bi-clock-history me-2"></i> Karyawan Terlambat Hari Ini</h3>
                </div>
                <div class="card-body table-responsive p-0">
                    <table class="table table-hover text-nowrap m-0">
                        <thead>
                            <tr>
                                <th>Karyawan</th>
                                <th>Lokasi Kerja</th>
                                <th>Jadwal Masuk</th>
                                <th>Masuk Aktual</th>
                                <th>Keterlambatan</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($lateEmployees as $att)
                                <tr>
                                    <td>
                                        <div>{{ $att->user->name ?? '-' }}</div>
                                        <div class="text-muted small">{{ $att->user->nik ?? '-' }}</div>
                                    </td>
                                    <td>{{ $att->user->location->name ?? '-' }}</td>
                                    <td>{{ $att->scheduled_time }}</td>
                                    <td><span class="text-danger fw-bold">{{ $att->actual_time }}</span></td>
                                    <td>
                                        <span class="badge text-bg-danger">{{ $att->late_minutes }} Menit</span>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="text-center text-muted">Hebat! Tidak ada karyawan yang terlambat hari ini.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-12">
            <div class="card mb-4">
                <div class="card-header text-bg-info">
                    <h3 class="card-title"><i class="bi bi-box-arrow-left me-2"></i> Karyawan Keluar Awal Hari Ini</h3>
                </div>
                <div class="card-body table-responsive p-0">
                    <table class="table table-hover text-nowrap m-0">
                        <thead>
                            <tr>
                                <th>Karyawan</th>
                                <th>Lokasi Kerja</th>
                                <th>Jadwal Keluar</th>
                                <th>Keluar Aktual</th>
                                <th>Pulang Cepat</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($earlyEmployees as $att)
                                <tr>
                                    <td>
                                        <div>{{ $att->user->name ?? '-' }}</div>
                                        <div class="text-muted small">{{ $att->user->nik ?? '-' }}</div>
                                    </td>
                                    <td>{{ $att->user->location->name ?? '-' }}</td>
                                    <td>{{ $att->scheduled_end_time }}</td>
                                    <td><span class="text-warning fw-bold">{{ $att->actual_end_time }}</span></td>
                                    <td>
                                        <span class="badge text-bg-warning">{{ $att->early_minutes }} Menit</span>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="text-center text-muted">Tidak ada karyawan yang pulang lebih awal hari ini.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-md-12">
            <div class="card mb-4">
                <div class="card-header text-bg-danger">
                    <h3 class="card-title"><i class="bi bi-person-x-fill me-2"></i> Karyawan Absen Hari Ini</h3>
                </div>
                <div class="card-body table-responsive p-0">
                    <table class="table table-hover text-nowrap m-0">
                        <thead>
                            <tr>
                                <th>Karyawan</th>
                                <th>Lokasi Kerja</th>
                                <th>Jadwal Masuk</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($absentEmployees as $user)
                                <tr>
                                    <td>
                                        <div>{{ $user->name ?? '-' }}</div>
                                        <div class="text-muted small">{{ $user->nik ?? '-' }}</div>
                                    </td>
                                    <td>{{ $user->location->name ?? '-' }}</td>
                                    <td>
                                        -
                                    </td>
                                    <td>
                                        <span class="badge text-bg-danger">Belum Hadir</span>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="text-center text-muted">Luar biasa! Semua karyawan hadir hari ini.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
@endsection