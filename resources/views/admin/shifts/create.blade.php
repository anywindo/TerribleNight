@extends('layouts.admin')

@section('title', 'Tambah Shift')
@section('page_title', 'Tambah Shift Baru')

@section('content')
<div class="row">
    <div class="col-12">
        <div class="card">
            <form action="{{ route('admin.shifts.store') }}" method="POST">
                @csrf
                <div class="card-body">
                    <div class="mb-3">
                        <label class="form-label">Nama Shift</label>
                        <input type="text" name="shift_name" class="form-control @error('shift_name') is-invalid @enderror" value="{{ old('shift_name') }}" placeholder="Contoh: Pagi, Siang, Malam" required>
                        @error('shift_name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Jam Masuk (Default)</label>
                            <input type="time" name="default_start_time" class="form-control @error('default_start_time') is-invalid @enderror" value="{{ old('default_start_time', '08:00') }}" required>
                            @error('default_start_time')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Jam Keluar (Default)</label>
                            <input type="time" name="default_end_time" class="form-control @error('default_end_time') is-invalid @enderror" value="{{ old('default_end_time', '17:00') }}" required>
                            @error('default_end_time')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                    </div>
                </div>
                <div class="card-footer">
                    <button type="submit" class="btn btn-primary">Simpan</button>
                    <a href="{{ route('admin.shifts.index') }}" class="btn btn-default float-end">Batal</a>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
