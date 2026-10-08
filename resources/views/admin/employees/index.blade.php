@extends('layouts.admin')

@section('title', 'Data Karyawan')
@section('page_title', 'Manajemen Karyawan')

@section('content')
<div class="row">
    <div class="col-12">
        @if(session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif
        @if(session('error'))
            <div class="alert alert-danger">{{ session('error') }}</div>
        @endif

        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center flex-wrap gap-2">
                <h3 class="card-title mb-0">Daftar Karyawan</h3>
                <div class="d-flex align-items-center ms-auto gap-2">
                    <form action="{{ route('admin.employees.index') }}" method="GET" class="d-flex">
                        <div class="input-group input-group-sm">
                            <input type="text" name="search" class="form-control" placeholder="Cari nama, email, NIK..." value="{{ request('search') }}">
                            <button type="submit" class="btn btn-default">
                                <i class="bi bi-search"></i>
                            </button>
                        </div>
                    </form>
                    <a href="#" data-bs-toggle="modal" data-bs-target="#importModal" class="btn btn-info btn-sm text-nowrap text-white"><i class="bi bi-file-earmark-arrow-up"></i> Import Excel</a>
                    <a href="{{ route('admin.employees.export') }}" class="btn btn-success btn-sm text-nowrap"><i class="bi bi-file-earmark-excel"></i> Export Excel</a>
                    <a href="{{ route('admin.employees.create') }}" class="btn btn-primary btn-sm text-nowrap"><i class="bi bi-plus"></i> Tambah Karyawan</a>
                </div>
            </div>
            <div class="card-body table-responsive p-0">
                <table class="table table-hover text-nowrap">
                    <thead>
                        <tr>
                            <th>Avatar</th>
                            <th>ID / NIK</th>
                            <th>Nama</th>
                            <th>Email</th>
                            <th>Role</th>
                            <th>Status</th>
                            <th>Lokasi Kerja</th>
                            <th style="width: 150px">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($employees as $employee)
                        <tr>
                            <td>
                                @if($employee->avatar && Storage::disk('public')->exists($employee->avatar))
                                    <a href="javascript:void(0)" onclick="showLightbox('{{ Storage::url($employee->avatar) }}')" title="Lihat Foto">
                                        <img src="{{ Storage::url($employee->avatar) }}" alt="Avatar" class="rounded" style="width: 40px; height: 40px; object-fit: cover;">
                                    </a>
                                @else
                                    <a href="javascript:void(0)" onclick="showLightbox('{{ asset('placeholder.svg') }}')" title="Tidak ada foto">
                                        <img src="{{ asset('placeholder.svg') }}" alt="Default Avatar" class="rounded" style="width: 40px; height: 40px; object-fit: cover;">
                                    </a>
                                @endif
                            </td>
                            <td>
                                <div>EMP{{ str_pad($employee->id, 4, '0', STR_PAD_LEFT) }}</div>
                                <div class="text-muted small">{{ $employee->nik ?: '-' }}</div>
                            </td>
                            <td>{{ $employee->name }}</td>
                            <td>{{ $employee->email }}</td>
                            <td>
                                @forelse($employee->roles->unique('name') as $role)
                                    <span class="badge text-bg-primary">{{ $role->name }}</span>
                                @empty
                                    <span class="text-muted small">Tidak ada</span>
                                @endforelse
                            </td>
                            <td>
                                @if($employee->is_active)
                                    <span class="badge text-bg-success">Aktif</span>
                                @else
                                    <span class="badge text-bg-danger">Nonaktif</span>
                                @endif
                            </td>
                            <td>{{ $employee->location ? $employee->location->name : '-' }}</td>
                            <td>
                                <a href="{{ route('admin.employees.edit', $employee) }}" class="btn btn-sm btn-warning"><i class="bi bi-pencil"></i></a>
                                @if($employee->id != auth()->id())
                                <form action="{{ route('admin.employees.destroy', $employee) }}" method="POST" class="d-inline" onsubmit="return confirm('Yakin ingin menghapus karyawan ini?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-danger"><i class="bi bi-trash"></i></button>
                                </form>
                                @endif
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="8" class="text-center">Belum ada data karyawan.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div class="card-footer clearfix">
                {{ $employees->links() }}
            </div>
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

<!-- Modal Import -->
<div class="modal fade" id="importModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <form action="{{ route('admin.employees.import') }}" method="POST" enctype="multipart/form-data" class="modal-content">
            @csrf
            <div class="modal-header">
                <h5 class="modal-title">Import Data Karyawan</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="mb-3">
                    <label class="form-label">Upload File Excel</label>
                    <input type="file" name="file" class="form-control" accept=".xlsx,.xls,.csv" required>
                    <div class="form-text">Pastikan file sesuai dengan template yang disediakan.</div>
                </div>
                <div class="mb-0 text-center">
                    <a href="{{ route('admin.employees.template') }}" class="btn btn-outline-secondary btn-sm">
                        <i class="bi bi-download"></i> Download Template
                    </a>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                <button type="submit" class="btn btn-primary"><i class="bi bi-upload"></i> Import</button>
            </div>
        </form>
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
