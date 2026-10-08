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
                    <a href="{{ route('admin.employees.create') }}" class="btn btn-primary btn-sm text-nowrap"><i class="bi bi-plus"></i> Tambah Karyawan</a>
                </div>
            </div>
            <div class="card-body table-responsive p-0">
                <table class="table table-hover text-nowrap">
                    <thead>
                        <tr>
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
                                <div>{{ $employee->id }}</div>
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
                            <td colspan="7" class="text-center">Belum ada data karyawan.</td>
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
@endsection
