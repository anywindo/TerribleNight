@extends('layouts.admin')

@section('title', 'Tambah Karyawan')
@section('page_title', 'Tambah Karyawan')

@section('content')
<div class="row">
    <div class="col-12">
        <div class="card">
            <form action="{{ route('admin.employees.store') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Nama Lengkap <span class="text-danger">*</span></label>
                            <input type="text" name="name" class="form-control @error('name') is-invalid @enderror" value="{{ old('name') }}" required>
                            @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Email <span class="text-danger">*</span></label>
                            <input type="email" name="email" class="form-control @error('email') is-invalid @enderror" value="{{ old('email') }}" required>
                            @error('email')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Employee ID (Opsional)</label>
                            <input type="text" name="employee_id" class="form-control @error('employee_id') is-invalid @enderror" value="{{ old('employee_id') }}" placeholder="Otomatis (contoh: EMP-00331)">
                            <div class="form-text">Kosongkan jika ingin sistem men-generate otomatis.</div>
                            @error('employee_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">NIK (Nomor Induk Karyawan)</label>
                            <input type="text" name="nik" class="form-control @error('nik') is-invalid @enderror" value="{{ old('nik') }}" placeholder="16 digit NIK">
                            @error('nik')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Tanggal Lahir (DOB) <span class="text-danger">*</span></label>
                            <input type="date" name="dob" class="form-control @error('dob') is-invalid @enderror" value="{{ old('dob') }}" required>
                            <div class="form-text">Default password otomatis memakai tanggal lahir dengan format DDMMYYYY (contoh: 12072004).</div>
                            @error('dob')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Lokasi Kerja <span class="text-danger">*</span></label>
                            <select name="location_id" class="form-select @error('location_id') is-invalid @enderror" required>
                                <option value="" disabled {{ old('location_id') ? '' : 'selected' }}>-- Pilih Lokasi Kerja --</option>
                                @foreach($locations as $location)
                                    <option value="{{ $location->id }}" {{ old('location_id') == $location->id ? 'selected' : '' }}>{{ $location->name }}</option>
                                @endforeach
                            </select>
                            @error('location_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Password (Opsional)</label>
                            <input type="password" name="password" class="form-control @error('password') is-invalid @enderror" placeholder="Kosongkan untuk gunakan Tanggal Lahir (DDMMYYYY)">
                            <div class="form-text">Jika dikosongkan, password otomatis memakai tanggal lahir karyawan (format DDMMYYYY).</div>
                            @error('password')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Konfirmasi Password</label>
                            <input type="password" name="password_confirmation" class="form-control" placeholder="Ulangi password jika mengisi password manual">
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Role Akses (Dashboard)</label>
                            <select name="role" class="form-select @error('role') is-invalid @enderror">
                                @foreach($roles as $role)
                                    <option value="{{ $role->name }}" {{ old('role', 'Employee') == $role->name ? 'selected' : '' }}>{{ $role->name }}</option>
                                @endforeach
                            </select>
                            @error('role')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-md-6 mb-3 d-flex align-items-end">
                            <div class="form-check mb-2">
                                <input class="form-check-input" type="checkbox" name="is_active" id="is_active" value="1" checked>
                                <label class="form-check-label" for="is_active">
                                    Akun Aktif (Bisa Login)
                                </label>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Foto Profil (Avatar)</label>
                            <input type="file" name="avatar" class="form-control @error('avatar') is-invalid @enderror" accept="image/*">
                            <div class="form-text">Maksimal 5MB. Format: JPG, PNG.</div>
                            @error('avatar')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                    </div>
                </div>
                <div class="card-footer">
                    <button type="submit" class="btn btn-primary">Simpan</button>
                    <a href="{{ route('admin.employees.index') }}" class="btn btn-default float-end">Batal</a>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
