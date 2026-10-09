<!DOCTYPE html>
<html lang="id">

<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
    <title>@yield('title', 'Admin Dashboard') | TerribleNight</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <!--begin::Fonts-->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@fontsource/source-sans-3@5.0.12/index.css"
        crossorigin="anonymous">
    <!--begin::Third Party Plugin(OverlayScrollbars)-->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/overlayscrollbars@2.3.0/styles/overlayscrollbars.min.css"
        crossorigin="anonymous">
    <!--begin::Third Party Plugin(Bootstrap Icons)-->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.min.css"
        crossorigin="anonymous">
    <!--begin::Required Plugin(AdminLTE)-->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/admin-lte@4.0.0/dist/css/adminlte.min.css">
    <style>
        :root {
            --bs-primary: #4f46e5;
            --bs-primary-rgb: 79, 70, 229;
            --bs-success: #10b981;
            --bs-success-rgb: 16, 185, 129;
            --bs-info: #0ea5e9;
            --bs-info-rgb: 14, 165, 233;
            --bs-warning: #f59e0b;
            --bs-warning-rgb: 245, 158, 11;
            --bs-danger: #ef4444;
            --bs-danger-rgb: 239, 68, 68;
            --bs-dark: #0f172a;
            --bs-dark-rgb: 15, 23, 42;
            --bs-body-bg: #f8fafc;
        }
        body {
            background: linear-gradient(135deg, #e0eafc 0%, #cfdef3 100%);
            background-attachment: fixed;
        }
        
        .app-header {
            background: rgba(255, 255, 255, 0.8) !important;
            backdrop-filter: blur(12px);
            -webkit-backdrop-filter: blur(12px);
            border-bottom: 1px solid rgba(0, 0, 0, 0.15);
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.08);
        }

        .card {
            background: rgba(255, 255, 255, 0.7);
            backdrop-filter: blur(16px);
            -webkit-backdrop-filter: blur(16px);
            border: 1px solid rgba(0, 0, 0, 0.15);
            box-shadow: 
                0 8px 12px -3px rgba(0, 0, 0, 0.1),
                inset 0 1px 0 rgba(255, 255, 255, 0.8),
                inset 1px 0 0 rgba(255, 255, 255, 0.4);
            border-radius: 1.25rem;
            overflow: hidden;
        }

        .card-header {
            border-bottom: 1px solid rgba(0, 0, 0, 0.15);
            background: rgba(255, 255, 255, 0.4);
            font-weight: 600;
        }

        .small-box {
            border-radius: 1.25rem;
            backdrop-filter: blur(10px);
            -webkit-backdrop-filter: blur(10px);
            border: 1px solid rgba(0, 0, 0, 0.15);
            box-shadow: 
                0 10px 15px -3px rgba(0, 0, 0, 0.15),
                inset 0 1px 0 rgba(255, 255, 255, 0.5);
            transition: all 0.3s cubic-bezier(0.25, 0.8, 0.25, 1);
            position: relative;
            overflow: hidden;
        }

        /* Skeuomorphic glossy overlay for small-box */
        .small-box::before {
            content: '';
            position: absolute;
            top: 0; left: 0; right: 0; height: 40%;
            background: linear-gradient(to bottom, rgba(255,255,255,0.4) 0%, rgba(255,255,255,0) 100%);
            pointer-events: none;
        }

        .small-box:hover {
            transform: translateY(-5px) scale(1.02);
            box-shadow: 
                0 20px 25px -5px rgba(0, 0, 0, 0.2),
                inset 0 1px 0 rgba(255, 255, 255, 0.8);
        }

        .btn {
            border-radius: 0.375rem;
            font-weight: 500;
            backdrop-filter: blur(5px);
            border: 1px solid rgba(0, 0, 0, 0.2);
            transition: all 0.3s ease;
        }
        
        .btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
        }

        .table {
            --bs-table-bg: transparent;
        }

        .table> :not(caption)>*>* {
            padding: 1rem;
            border-bottom-color: rgba(0, 0, 0, 0.15);
        }
        
        .table-hover tbody tr {
            transition: all 0.2s ease;
        }
        
        .table-hover tbody tr:hover {
            background-color: rgba(255, 255, 255, 0.3) !important;
            transform: scale(1.01);
            box-shadow: 0 4px 6px -1px rgba(0,0,0,0.05);
        }

        .app-footer, .sidebar-footer {
            height: 65px;
            display: flex;
            align-items: center;
            padding: 0 1.25rem;
        }

        .app-footer {
            justify-content: space-between;
        }

        /* Animations */
        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(20px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        @keyframes subtleFloat {
            0% { transform: translateY(0); }
            50% { transform: translateY(-3px); }
            100% { transform: translateY(0); }
        }

        .app-content .row {
            animation: fadeInUp 0.6s cubic-bezier(0.16, 1, 0.3, 1) forwards;
            opacity: 0;
        }

        /* Stagger the rows loading */
        .app-content .row:nth-child(1) { animation-delay: 0.1s; }
        .app-content .row:nth-child(2) { animation-delay: 0.2s; }
        .app-content .row:nth-child(3) { animation-delay: 0.3s; }
        .app-content .row:nth-child(4) { animation-delay: 0.4s; }

        .small-box-icon {
            animation: subtleFloat 3s ease-in-out infinite;
        }
    </style>
    @stack('styles')
</head>

<body class="layout-fixed sidebar-expand-lg bg-body-tertiary">
    <!--begin::App Wrapper-->
    <div class="app-wrapper">

        <!--begin::Header-->
        <nav class="app-header navbar navbar-expand bg-body">
            <div class="container-fluid">
                <!--begin::Start Navbar Links-->
                <ul class="navbar-nav">
                    <li class="nav-item">
                        <a class="nav-link" data-lte-toggle="sidebar" href="#" role="button">
                            <i class="bi bi-list"></i>
                        </a>
                    </li>
                    <li class="nav-item d-none d-md-flex align-items-center ms-2">
                        <span class="fw-bold fs-5" style="color: rgba(15, 23, 42, 0.85); text-shadow: 0 1px 2px rgba(255,255,255,0.8);">
                            Sistem Informasi Manajemen SDM Garment
                        </span>
                    </li>
                </ul>
                <!--end::Start Navbar Links-->

                <!--begin::End Navbar Links-->
                <ul class="navbar-nav ms-auto">
                    <!--begin::User Menu Dropdown-->
                    <li class="nav-item dropdown user-menu">
                        <a href="#" class="nav-link dropdown-toggle" data-bs-toggle="dropdown">
                            <img src="{{ auth()->user()->avatar && Storage::disk('public')->exists(auth()->user()->avatar) ? Storage::url(auth()->user()->avatar) : asset('userdefault-160x160.jpg') }}"
                                class="user-image rounded-circle shadow" alt="User Image">
                            <span class="d-none d-md-inline">{{ auth()->user()->name }}</span>
                        </a>
                        <ul class="dropdown-menu dropdown-menu-lg dropdown-menu-end shadow-lg" style="border-radius: 1rem; overflow: hidden; border: 1px solid rgba(0,0,0,0.1);">
                            <li class="user-header text-bg-primary" style="background: linear-gradient(135deg, #4f46e5 0%, #3b82f6 100%) !important;">
                                <img src="{{ auth()->user()->avatar && Storage::disk('public')->exists(auth()->user()->avatar) ? Storage::url(auth()->user()->avatar) : asset('userdefault-160x160.jpg') }}"
                                    class="rounded-circle shadow-sm border border-2 border-white mb-2" alt="User Image">
                                <p class="mb-0 fw-bold fs-5">
                                    {{ auth()->user()->name }}
                                </p>
                                <div class="mt-1">
                                    <span class="badge bg-light text-primary fw-bold px-2 py-1">{{ auth()->user()->roles->pluck('name')->join(', ') }}</span>
                                </div>
                                <small class="d-block mt-2 opacity-75">
                                    <i class="bi bi-person-badge"></i> {{ auth()->user()->employee_id ?? 'N/A' }} &bull; {{ auth()->user()->email }}
                                </small>
                            </li>
                            <li class="user-footer bg-light p-3">
                                <div class="d-flex justify-content-between mb-2">
                                    <a href="#" class="btn btn-outline-secondary btn-sm rounded-pill px-3 fw-bold flex-fill me-1">
                                        <i class="bi bi-person-circle me-1"></i> Profil Saya
                                    </a>
                                    <a href="{{ route('role.selection') }}" class="btn btn-outline-primary btn-sm rounded-pill px-3 fw-bold flex-fill ms-1">
                                        <i class="bi bi-arrow-left-right me-1"></i> Ganti Portal
                                    </a>
                                </div>
                                <form method="POST" action="{{ route('logout') }}" class="d-block w-100">
                                    @csrf
                                    <button type="submit" class="btn btn-danger btn-sm rounded-pill fw-bold w-100">
                                        <i class="bi bi-box-arrow-right me-1"></i> Logout
                                    </button>
                                </form>
                            </li>
                        </ul>
                    </li>
                    <!--end::User Menu Dropdown-->
                </ul>
                <!--end::End Navbar Links-->
            </div>
        </nav>
        <!--end::Header-->

        <!--begin::Sidebar-->
        @include('layouts.partials.sidebar')
        <!--end::Sidebar-->

        <!--begin::App Main-->
        <main class="app-main">
            <!--begin::App Content Header-->
            <div class="app-content-header">
                <div class="container-fluid">
                    <div class="row">
                        <div class="col-sm-6">
                            <h3 class="mb-0">@yield('page_title', 'Dashboard')</h3>
                        </div>
                        <div class="col-sm-6">
                            <ol class="breadcrumb float-sm-end">
                                <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}"><i class="bi bi-house-door-fill"></i> Home</a></li>
                                @foreach(request()->segments() as $segment)
                                    @if(strtolower($segment) !== 'admin')
                                        <li class="breadcrumb-item {{ $loop->last ? 'active' : '' }}" {{ $loop->last ? 'aria-current="page"' : '' }}>
                                            {{ ucfirst(str_replace('-', ' ', $segment)) }}
                                        </li>
                                    @endif
                                @endforeach
                            </ol>
                        </div>
                    </div>
                </div>
            </div>
            <!--end::App Content Header-->

            <!--begin::App Content-->
            <div class="app-content">
                <div class="container-fluid">
                    @yield('content')
                </div>
            </div>
            <!--end::App Content-->
        </main>
        <!--end::App Main-->

        <!--begin::Footer-->
        <footer class="app-footer">
            <div>
                <strong>Copyright &copy; {{ date('Y') }} <a href="https://kiranadesainindonesia.com" target="_blank">Kirana Group</a>.</strong> All rights reserved.
            </div>
            <div class="d-none d-sm-inline">TerribleNight System</div>
        </footer>
        <!--end::Footer-->
    </div>
    <!--end::App Wrapper-->

    <!--begin::Third Party Plugin(OverlayScrollbars)-->
    <script src="https://cdn.jsdelivr.net/npm/overlayscrollbars@2.3.0/browser/overlayscrollbars.browser.es6.min.js"
        crossorigin="anonymous"></script>
    @include('layouts.partials.lightbox')

    <!--begin::Required Plugin(popperjs for Bootstrap 5)-->
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.8/dist/umd/popper.min.js"
        crossorigin="anonymous"></script>
    <!--begin::Required Plugin(Bootstrap 5)-->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.min.js"
        crossorigin="anonymous"></script>
    <!--begin::Required Plugin(AdminLTE)-->
    <script src="https://cdn.jsdelivr.net/npm/admin-lte@4.0.0/dist/js/adminlte.min.js"></script>
    <!--begin::SweetAlert2 for Toasts-->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        const Toast = Swal.mixin({
            toast: true,
            position: 'top-end',
            showConfirmButton: false,
            timer: 3000,
            timerProgressBar: true,
            didOpen: (toast) => {
                toast.addEventListener('mouseenter', Swal.stopTimer)
                toast.addEventListener('mouseleave', Swal.resumeTimer)
            }
        });

        @if(session('success'))
            Toast.fire({
                icon: 'success',
                title: '{{ session('success') }}'
            });
        @endif

        @if(session('error'))
            Toast.fire({
                icon: 'error',
                title: '{{ session('error') }}'
            });
        @endif
        
        @if($errors->any())
            Toast.fire({
                icon: 'error',
                title: 'Terdapat kesalahan pada input Anda!'
            });
        @endif
    </script>
    @stack('scripts')
</body>

</html>