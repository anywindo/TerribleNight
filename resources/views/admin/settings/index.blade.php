@extends('layouts.admin')

@section('page_title', 'Pengaturan')

@section('content')
<div class="container-fluid">
    <form action="{{ route('admin.settings.update') }}" method="POST">
        @csrf
        <div class="row">
            <div class="col-md-6">
                <div class="card mb-4">
                    <div class="card-header">
                        <h3 class="card-title">Pengaturan Rule Presensi</h3>
                    </div>
                    <div class="card-body">
                        @if (session('success'))
                            <div class="alert alert-success">
                                {{ session('success') }}
                            </div>
                        @endif

                        <div class="form-group">
                            <div class="custom-control custom-switch">
                                <input type="checkbox" class="custom-control-input" id="attendance_rule_enabled" name="attendance_rule_enabled" value="1" {{ $attendanceRuleEnabled ? 'checked' : '' }}>
                                <label class="custom-control-label" for="attendance_rule_enabled">Aktifkan Batas Waktu Shift Start</label>
                            </div>
                            <small class="form-text text-muted">
                                Jika diaktifkan, karyawan wajib melakukan presensi shift start sebelum batas waktu yang ditentukan.
                            </small>
                        </div>

                        <div class="form-group mt-4">
                            <label for="attendance_rule_minutes">Batas Waktu (Menit sebelum shift dimulai)</label>
                            <input type="number" name="attendance_rule_minutes" id="attendance_rule_minutes" class="form-control" value="{{ old('attendance_rule_minutes', $attendanceRuleMinutes) }}" min="0" required>
                            <small class="form-text text-muted">
                                Contoh: Jika diisi 15 dan shift mulai 08:00, maka karyawan wajib presensi sebelum pukul 07:45. Presensi pada 07:45 atau setelahnya akan ditolak.
                            </small>
                        </div>
                    </div>
                    <div class="card-footer">
                        <button type="submit" class="btn btn-primary">Simpan Pengaturan</button>
                    </div>
                </div>
            </div>
            
            <div class="col-md-6">
                <div class="card mb-4">
                    <div class="card-header">
                        <h3 class="card-title">Pengecualian Presensi</h3>
                    </div>
                    
                    <div class="card-body">
                        <div class="form-group">
                            <label>Pilih Role yang Dikecualikan</label>
                            <div class="row mt-2">
                                @foreach($roles as $role)
                                    <div class="col-md-6 mb-2">
                                        <div class="custom-control custom-checkbox">
                                            <input type="checkbox" class="custom-control-input" id="role_{{ $role->id }}" name="exclude_roles[]" value="{{ $role->name }}" {{ is_array($excludedRoles) && in_array($role->name, $excludedRoles) ? 'checked' : '' }}>
                                            <label class="custom-control-label" for="role_{{ $role->id }}">{{ ucfirst($role->name) }}</label>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                            <small class="form-text text-muted mt-3">
                                Role yang dipilih tidak akan diminta presensi dan tidak akan mendapatkan notifikasi atau masuk dalam daftar "belum absen".
                            </small>
                        </div>
                    </div>
                    <div class="card-footer">
                        <button type="submit" class="btn btn-primary">Simpan Pengaturan</button>
                    </div>
                </div>
            </div>
        </div>
    </form>

    <div class="row">
        <div class="col-md-6">
            <div class="card mb-4">
                <div class="card-header">
                    <h3 class="card-title">Import Data Karyawan</h3>
                </div>
                <form action="{{ route('admin.employees.import') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="card-body">
                        <div class="mb-3">
                            <label class="form-label">Upload File Excel (.xlsx, .xls, .csv)</label>
                            <input type="file" name="file" class="form-control" accept=".xlsx,.xls,.csv" required>
                            <div class="form-text mt-2">
                                Pastikan format file sesuai dengan template standar agar data karyawan masuk dengan benar ke dalam sistem.
                            </div>
                        </div>
                        <a href="{{ route('admin.employees.template') }}" class="btn btn-warning btn-sm fw-bold shadow-sm">
                            <i class="bi bi-download"></i> Download Template Excel
                        </a>
                    </div>
                    <div class="card-footer">
                        <button type="submit" class="btn btn-primary"><i class="bi bi-upload"></i> Mulai Import</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
