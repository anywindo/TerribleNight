@extends('layouts.admin')

@section('title', 'Dashboard HR')
@section('page_title', 'Dashboard')

@section('content')
    <div class="row">
        <div class="col-lg-3 col-6">
            <div class="small-box text-bg-primary">
                <div class="inner">
                    <h3>150</h3>
                    <p>Total Karyawan</p>
                </div>
                <div class="small-box-icon">
                    <i class="bi bi-people-fill"></i>
                </div>
                <a href="{{ route('admin.employees.index') }}"
                    class="small-box-footer link-light link-underline-opacity-0 link-underline-opacity-50-hover">
                    Selengkapnya <i class="bi bi-link-45deg"></i>
                </a>
            </div>
        </div>

        <div class="col-lg-3 col-6">
            <div class="small-box text-bg-success">
                <div class="inner">
                    <h3>142</h3>
                    <p>Hadir Hari Ini</p>
                </div>
                <div class="small-box-icon">
                    <i class="bi bi-person-check-fill"></i>
                </div>
                <a href="#" class="small-box-footer link-light link-underline-opacity-0 link-underline-opacity-50-hover">
                    Selengkapnya <i class="bi bi-link-45deg"></i>
                </a>
            </div>
        </div>

        <div class="col-lg-3 col-6">
            <div class="small-box text-bg-warning">
                <div class="inner">
                    <h3>5</h3>
                    <p>Pengajuan Cuti / Lembur</p>
                </div>
                <div class="small-box-icon">
                    <i class="bi bi-envelope-paper-fill"></i>
                </div>
                <a href="#" class="small-box-footer link-dark link-underline-opacity-0 link-underline-opacity-50-hover">
                    Selengkapnya <i class="bi bi-link-45deg"></i>
                </a>
            </div>
        </div>

        <div class="col-lg-3 col-6">
            <div class="small-box text-bg-danger">
                <div class="inner">
                    <h3>3</h3>
                    <p>Karyawan Absen</p>
                </div>
                <div class="small-box-icon">
                    <i class="bi bi-person-dash-fill"></i>
                </div>
                <a href="#" class="small-box-footer link-light link-underline-opacity-0 link-underline-opacity-50-hover">
                    Selengkapnya <i class="bi bi-link-45deg"></i>
                </a>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-12">
            <div class="card mb-4">
                <div class="card-header">
                    <h3 class="card-title">Selamat datang di HR Portal</h3>
                </div>
                <div class="card-body">
                    <p>Gunakan menu di sebelah kiri untuk mengelola master data karyawan, melihat riwayat presensi, atau
                        menyetujui pengajuan.</p>
                </div>
            </div>
        </div>
    </div>
@endsection