@extends('layouts.admin')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-md-6">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Pengaturan Rule Presensi</h3>
                </div>
                
                <form action="{{ route('admin.settings.update') }}" method="POST">
                    @csrf
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
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
