@extends('layouts.admin')

@section('title', 'Profil Saya')
@section('page_title', 'Profil Saya')

@section('content')
<div class="row">
    <div class="col-xl-4 col-lg-5 mb-4">
        <div class="card shadow-sm h-100 text-center">
            <div class="card-body d-flex flex-column justify-content-center align-items-center">
                <form action="{{ route('admin.profile.update') }}" method="POST" enctype="multipart/form-data" id="avatarForm">
                    @csrf
                    @method('PUT')
                    
                    <div class="position-relative d-inline-block mb-3">
                        <img src="{{ $user->avatar && Storage::disk('public')->exists($user->avatar) ? Storage::url($user->avatar) : asset('userdefault-160x160.jpg') }}" 
                             class="rounded-circle border border-4 border-primary shadow-sm" 
                             style="width: 150px; height: 150px; object-fit: cover;" id="avatarPreview">
                        
                        <label for="avatar" class="position-absolute bottom-0 end-0 bg-primary text-white rounded-circle p-2 shadow cursor-pointer" style="cursor: pointer; right: 5px; bottom: 5px;" title="Ubah Foto">
                            <i class="bi bi-camera-fill"></i>
                        </label>
                        <input type="file" name="avatar" id="avatar" class="d-none" accept="image/*" onchange="previewImage(this)">
                    </div>
                    @error('avatar')
                        <div class="text-danger small mt-2">{{ $message }}</div>
                    @enderror

                    <h4 class="fw-bold mb-1">{{ $user->name }}</h4>
                    <p class="text-muted mb-3">{{ $user->email }}</p>
                    <div class="badge bg-primary-subtle text-primary border border-primary-subtle px-3 py-2 rounded-pill mb-4">
                        {{ $user->roles->pluck('name')->unique()->join(', ') }}
                    </div>

                    <div class="d-grid w-100 px-4">
                        <button type="submit" class="btn btn-primary fw-bold" id="avatarSubmitBtn" style="display: none;">
                            <i class="bi bi-upload me-1"></i> Update Foto
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div class="col-xl-8 col-lg-7 mb-4">
        <div class="card shadow-sm h-100">
            <div class="card-header bg-white border-bottom p-4">
                <h4 class="card-title mb-0 fw-bold"><i class="bi bi-person-lines-fill me-2 text-primary"></i> Data Personal & Keamanan</h4>
            </div>
            <div class="card-body p-4">
                <form action="{{ route('admin.profile.update') }}" method="POST">
                    @csrf
                    @method('PUT')

                    <div class="row mb-4">
                        <div class="col-md-6 mb-3 mb-md-0">
                            <label class="form-label fw-bold">Nama Lengkap</label>
                            <div class="input-group">
                                <span class="input-group-text bg-light"><i class="bi bi-person"></i></span>
                                <input type="text" name="name" class="form-control @error('name') is-invalid @enderror" value="{{ old('name', $user->name) }}" required>
                            </div>
                            @error('name')
                                <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="col-md-6">
                            <label class="form-label fw-bold">Email</label>
                            <div class="input-group">
                                <span class="input-group-text bg-light"><i class="bi bi-envelope"></i></span>
                                <input type="email" name="email" class="form-control @error('email') is-invalid @enderror" value="{{ old('email', $user->email) }}" required>
                            </div>
                            @error('email')
                                <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="row mb-4">
                        <div class="col-md-6 mb-3 mb-md-0">
                            <label class="form-label fw-bold">Employee ID</label>
                            <div class="input-group">
                                <span class="input-group-text bg-light"><i class="bi bi-person-badge"></i></span>
                                <input type="text" class="form-control bg-light" value="{{ $user->employee_id ?? '-' }}" readonly disabled>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label fw-bold">NIK (KTP)</label>
                            <div class="input-group">
                                <span class="input-group-text bg-light"><i class="bi bi-credit-card-2-front"></i></span>
                                <input type="text" class="form-control bg-light" value="{{ $user->nik ?? '-' }}" readonly disabled>
                            </div>
                        </div>
                        <div class="col-12 mt-1">
                            <small class="text-muted"><i class="bi bi-info-circle me-1"></i>Hubungi administrator jika ada kesalahan pada data ini.</small>
                        </div>
                    </div>
                    
                    <hr class="my-5 opacity-25">
                    
                    <h5 class="fw-bold mb-4 text-secondary"><i class="bi bi-shield-lock me-2"></i>Ubah Password <span class="fs-6 fw-normal text-muted">(Opsional)</span></h5>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-bold">Password Baru</label>
                            <div class="input-group">
                                <span class="input-group-text bg-light"><i class="bi bi-key"></i></span>
                                <input type="password" name="password" class="form-control @error('password') is-invalid @enderror" placeholder="Kosongkan jika tak ingin diubah">
                            </div>
                            @error('password')
                                <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-6 mb-4">
                            <label class="form-label fw-bold">Konfirmasi Password</label>
                            <div class="input-group">
                                <span class="input-group-text bg-light"><i class="bi bi-key-fill"></i></span>
                                <input type="password" name="password_confirmation" class="form-control" placeholder="Ulangi password baru">
                            </div>
                        </div>
                    </div>

                    <div class="d-flex justify-content-end mt-2">
                        <button type="submit" class="btn btn-primary px-4 py-2 fw-bold shadow-sm">
                            <i class="bi bi-floppy me-1"></i> Simpan Perubahan
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
function previewImage(input) {
    if (input.files && input.files[0]) {
        var reader = new FileReader();
        reader.onload = function(e) {
            document.getElementById('avatarPreview').src = e.target.result;
            // Tampilkan tombol submit untuk form avatar
            document.getElementById('avatarSubmitBtn').style.display = 'block';
        }
        reader.readAsDataURL(input.files[0]);
    }
}
</script>
@endpush
@endsection
