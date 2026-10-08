<!DOCTYPE html>
<html lang="id">

<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
    <title>Pilih Portal | TerribleNight</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <!--begin::Fonts-->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@fontsource/source-sans-3@5.0.12/index.css"
        crossorigin="anonymous">
    <!--begin::Third Party Plugin(Bootstrap Icons)-->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.min.css"
        crossorigin="anonymous">
    <!--begin::Required Plugin(AdminLTE)-->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/admin-lte@4.0.0/dist/css/adminlte.min.css">
    <style>
        .portal-card {
            transition: transform 0.2s, box-shadow 0.2s;
            cursor: pointer;
            text-decoration: none;
            color: inherit;
        }

        .portal-card:hover {
            transform: translateY(-3px);
            box-shadow: 0 .5rem 1rem rgba(0, 0, 0, .15) !important;
            color: inherit;
        }
    </style>
</head>

<body class="login-page bg-body-secondary">
    <div class="login-box" style="width: 450px; max-width: 90vw;">
        <div class="login-logo mb-4">
            <a href="/"><b>KIRANA</b> GROUP</a>
        </div>

        <div class="text-center mb-4">
            <h5 class="mb-1">Selamat datang, <b>{{ auth()->user()->name }}</b></h5>
            <p class="text-muted small">Silakan pilih portal untuk melanjutkan</p>
        </div>

        <div class="row g-3">
            <div class="col-12">
                <a href="{{ route('admin.dashboard') }}" class="card portal-card shadow-sm h-100">
                    <div class="card-body d-flex align-items-center">
                        <div class="flex-shrink-0 bg-primary bg-opacity-10 text-primary rounded-circle p-3 me-3 d-flex align-items-center justify-content-center"
                            style="width: 50px; height: 50px;">
                            <i class="bi bi-database-fill fs-4"></i>
                        </div>
                        <div class="flex-grow-1 text-start pe-3">
                            <h6 class="mb-1 fw-bold">Portal HR / Admin</h6>
                            <p class="text-muted small mb-0">Kelola database, data karyawan & laporan</p>
                        </div>
                        <div class="ms-auto">
                            <i class="bi bi-chevron-right text-muted"></i>
                        </div>
                    </div>
                </a>
            </div>

            <div class="col-12">
                <a href="{{ route('attendance.index') }}" class="card portal-card shadow-sm h-100">
                    <div class="card-body d-flex align-items-center">
                        <div class="flex-shrink-0 bg-warning bg-opacity-10 text-warning rounded-circle p-3 me-3 d-flex align-items-center justify-content-center"
                            style="width: 50px; height: 50px;">
                            <i class="bi bi-fingerprint fs-4"></i>
                        </div>
                        <div class="flex-grow-1 text-start pe-3">
                            <h6 class="mb-1 fw-bold">Portal Karyawan</h6>
                            <p class="text-muted small mb-0">Masuk untuk presensi mandiri</p>
                        </div>
                        <div class="ms-auto">
                            <i class="bi bi-chevron-right text-muted"></i>
                        </div>
                    </div>
                </a>
            </div>
        </div>

        <div class="text-center mt-5">
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit" class="btn btn-link text-secondary text-decoration-none hover-danger">
                    <i class="bi bi-box-arrow-right me-1"></i> Logout
                </button>
            </form>
        </div>
    </div>
</body>

</html>