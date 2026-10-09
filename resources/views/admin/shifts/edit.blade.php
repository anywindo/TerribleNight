@extends('layouts.admin')

@section('title', 'Edit Shift')
@section('page_title', 'Edit Shift: ' . $shift->shift_name)

@section('content')
<div class="row">
    <div class="col-12">
        <div class="card">
            <form action="{{ route('admin.shifts.update', $shift) }}" method="POST">
                @csrf
                @method('PUT')
                <div class="card-body">
                    <div class="mb-3">
                        <label class="form-label">Nama Shift</label>
                        <input type="text" name="shift_name" class="form-control @error('shift_name') is-invalid @enderror" value="{{ old('shift_name', $shift->shift_name) }}" required>
                        @error('shift_name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Jam Masuk (Default)</label>
                            <input type="time" name="default_start_time" class="form-control @error('default_start_time') is-invalid @enderror" value="{{ old('default_start_time', \Carbon\Carbon::parse($shift->default_start_time)->format('H:i')) }}" required>
                            @error('default_start_time')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Jam Keluar (Default)</label>
                            <input type="time" name="default_end_time" class="form-control @error('default_end_time') is-invalid @enderror" value="{{ old('default_end_time', \Carbon\Carbon::parse($shift->default_end_time)->format('H:i')) }}" required>
                            @error('default_end_time')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Jam Mulai Istirahat (Opsional)</label>
                            <input type="time" name="break_start" class="form-control @error('break_start') is-invalid @enderror" value="{{ old('break_start', $shift->break_start ? \Carbon\Carbon::parse($shift->break_start)->format('H:i') : '') }}">
                            @error('break_start')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Jam Selesai Istirahat (Opsional)</label>
                            <input type="time" name="break_end" class="form-control @error('break_end') is-invalid @enderror" value="{{ old('break_end', $shift->break_end ? \Carbon\Carbon::parse($shift->break_end)->format('H:i') : '') }}">
                            @error('break_end')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                    </div>
                </div>
                <div class="card-footer">
                    <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
                    <a href="{{ route('admin.shifts.index') }}" class="btn btn-default float-end">Batal</a>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
