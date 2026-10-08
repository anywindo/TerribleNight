@extends('layouts.admin')

@section('title', 'Pengaturan Shift')
@section('page_title', 'Pengaturan Shift')

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
            <div class="card-header d-flex justify-content-between align-items-center">
                <h3 class="card-title">Daftar Shift Kerja</h3>
                <div class="ms-auto">
                    <a href="{{ route('admin.shifts.create') }}" class="btn btn-primary btn-sm"><i class="bi bi-plus"></i> Tambah Shift</a>
                </div>
            </div>
            <div class="card-body table-responsive p-0">
                <table class="table table-hover text-nowrap">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Nama Shift</th>
                            <th>Jam Masuk Default</th>
                            <th>Jam Keluar Default</th>
                            <th style="width: 150px">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($shifts as $shift)
                        <tr>
                            <td>{{ $shift->id }}</td>
                            <td>{{ $shift->shift_name }}</td>
                            <td>{{ \Carbon\Carbon::parse($shift->default_start_time)->format('H:i') }}</td>
                            <td>{{ \Carbon\Carbon::parse($shift->default_end_time)->format('H:i') }}</td>
                            <td>
                                <a href="{{ route('admin.shifts.edit', $shift) }}" class="btn btn-sm btn-warning"><i class="bi bi-pencil"></i></a>
                                <form action="{{ route('admin.shifts.destroy', $shift) }}" method="POST" class="d-inline" onsubmit="return confirm('Yakin ingin menghapus shift ini?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-danger"><i class="bi bi-trash"></i></button>
                                </form>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="5" class="text-center">Belum ada data shift.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div class="card-footer clearfix">
                {{ $shifts->links() }}
            </div>
        </div>
    </div>
</div>
@endsection
