@extends('layouts.admin')

@section('title', 'Edit Karyawan')
@section('page_title', 'Edit Karyawan')

@section('content')
<div class="row">
    <div class="col-12">
        <div class="card">
            <form action="{{ route('admin.employees.update', $employee) }}" method="POST" enctype="multipart/form-data">
                @csrf
                @method('PUT')
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Nama Lengkap</label>
                            <input type="text" name="name" class="form-control @error('name') is-invalid @enderror" value="{{ old('name', $employee->name) }}" required>
                            @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Email</label>
                            <input type="email" name="email" class="form-control @error('email') is-invalid @enderror" value="{{ old('email', $employee->email) }}" required>
                            @error('email')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">NIK (Nomor Induk Karyawan)</label>
                            <input type="text" name="nik" class="form-control @error('nik') is-invalid @enderror" value="{{ old('nik', $employee->nik) }}">
                            @error('nik')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Lokasi Kerja</label>
                            <select name="location_id" class="form-select @error('location_id') is-invalid @enderror">
                                <option value="">-- Tidak Ditentukan --</option>
                                @foreach($locations as $location)
                                    <option value="{{ $location->id }}" {{ old('location_id', $employee->location_id) == $location->id ? 'selected' : '' }}>{{ $location->name }}</option>
                                @endforeach
                            </select>
                            @error('location_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Password Baru (Opsional)</label>
                            <input type="password" name="password" class="form-control @error('password') is-invalid @enderror" placeholder="Isi jika ingin mengubah password">
                            @error('password')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-md-6 mb-3 d-flex align-items-end">
                            <div class="form-check mb-2">
                                <input class="form-check-input" type="checkbox" name="is_active" id="is_active" value="1" {{ $employee->is_active ? 'checked' : '' }}>
                                <label class="form-check-label" for="is_active">
                                    Akun Aktif (Bisa Login)
                                </label>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Role Akses (Dashboard)</label>
                            <select name="role" class="form-select @error('role') is-invalid @enderror">
                                @foreach($roles as $role)
                                    <option value="{{ $role->name }}" {{ $employee->hasRole($role->name) || ($employee->roles->count() === 0 && $role->name === 'Employee') ? 'selected' : '' }}>{{ $role->name }}</option>
                                @endforeach
                            </select>
                            @error('role')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Foto Profil (Avatar)</label>
                            <div class="d-flex align-items-center gap-3">
                                @if($employee->avatar && Storage::disk('public')->exists($employee->avatar))
                                    <a href="javascript:void(0)" onclick="showLightbox('{{ Storage::url($employee->avatar) }}')" title="Lihat Foto">
                                        <img src="{{ Storage::url($employee->avatar) }}" alt="Avatar" class="rounded" style="width: 50px; height: 50px; object-fit: cover;">
                                    </a>
                                @else
                                    <a href="javascript:void(0)" onclick="showLightbox('{{ asset('placeholder.svg') }}')" title="Tidak ada foto">
                                        <img src="{{ asset('placeholder.svg') }}" alt="Default Avatar" class="rounded" style="width: 50px; height: 50px; object-fit: cover;">
                                    </a>
                                @endif
                                <div>
                                    <input type="file" name="avatar" class="form-control form-control-sm @error('avatar') is-invalid @enderror" accept="image/*">
                                    <div class="form-text small">Maks 5MB. Biarkan kosong jika tidak ingin mengubah.</div>
                                    @error('avatar')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="card-footer">
                    <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
                    <a href="{{ route('admin.employees.index') }}" class="btn btn-default float-end">Batal</a>
                </div>
            </form>
        </div>
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
                <img id="lightboxImage" src="" class="img-fluid rounded" alt="Avatar" style="max-height: 80vh;">
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
</script>
@endsection
