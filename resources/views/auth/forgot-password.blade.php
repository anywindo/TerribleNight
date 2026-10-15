<!DOCTYPE html>
<html lang="id">
<!--begin::Head-->

<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
    <title>Lupa Kata Sandi | Kirana People</title>
    <!--begin::Primary Meta Tags-->
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    @include('layouts.partials.favicons')

    <!--begin::Fonts-->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700&display=swap"
        rel="stylesheet">
    <!--end::Fonts-->

    <!--begin::Third Party Plugin(Bootstrap Icons)-->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.min.css">
    <!--end::Third Party Plugin(Bootstrap Icons)-->

    <!--begin::Required Plugin(AdminLTE)-->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/admin-lte@4.0.0/dist/css/adminlte.min.css">
    <!--end::Required Plugin(AdminLTE)-->

    <style>
        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
        }

        .login-page {
            background-image: linear-gradient(rgba(0, 0, 0, 0.5), rgba(0, 0, 0, 0.5)), url('{{ asset('loginpage2.webp') }}');
            background-size: cover;
            background-position: center;
            background-repeat: no-repeat;
            background-attachment: fixed;
        }

        .login-box .card {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
            -webkit-backdrop-filter: blur(10px);
            border-radius: 1rem;
            box-shadow: 0 8px 32px rgba(0, 0, 0, 0.15);
        }

        .login-logo a {
            color: #fff !important;
            text-shadow: 0 2px 4px rgba(0, 0, 0, 0.5);
        }
    </style>
</head>
<!--end::Head-->

<body class="login-page bg-body-secondary">
    <div class="login-box">
        <div class="login-logo">
            <a href="/"><b>Kirana</b> People</a>
        </div>
        <!-- /.login-logo -->
        <div class="card">
            <div class="card-body login-card-body">
                <p class="login-box-msg fw-semibold mb-2">Lupa Kata Sandi?</p>
                <p class="text-muted small text-center mb-4">
                    Masukkan alamat email Anda yang terdaftar. Kami akan mengirimkan tautan reset kata sandi dan kode OTP verifikasi.
                </p>

                @if (session('status'))
                    <div class="alert alert-success d-flex align-items-center" role="alert">
                        <i class="bi bi-check-circle-fill me-2 flex-shrink-0"></i>
                        <div class="small">{{ session('status') }}</div>
                    </div>
                @endif

                @if (session('error'))
                    <div class="alert alert-danger d-flex align-items-center" role="alert">
                        <i class="bi bi-exclamation-triangle-fill me-2 flex-shrink-0"></i>
                        <div class="small">{{ session('error') }}</div>
                    </div>
                @endif

                @if ($errors->any())
                    <div class="alert alert-danger" role="alert">
                        <ul class="mb-0 small ps-3">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <form action="{{ route('password.email') }}" method="post">
                    @csrf
                    <div class="input-group mb-3">
                        <input type="email" name="email" class="form-control" placeholder="Alamat Email Terdaftar"
                            value="{{ old('email') }}" required autofocus>
                        <div class="input-group-text">
                            <span class="bi bi-envelope-at"></span>
                        </div>
                    </div>

                    <div class="d-grid gap-2 mb-3">
                        <button type="submit" class="btn btn-primary fw-semibold py-2">
                            <i class="bi bi-send me-1"></i> Kirim Tautan Reset
                        </button>
                    </div>
                </form>

                <p class="mb-0 text-center">
                    <a href="{{ route('login') }}" class="text-decoration-none small text-muted">
                        <i class="bi bi-arrow-left me-1"></i> Kembali ke Halaman Masuk
                    </a>
                </p>
            </div>
            <!-- /.login-card-body -->
        </div>
    </div>
    <!-- /.login-box -->
</body>

</html>
