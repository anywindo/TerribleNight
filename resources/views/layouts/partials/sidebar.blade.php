<aside class="app-sidebar bg-body-secondary shadow" data-bs-theme="dark">
    <!--begin::Sidebar Brand-->
    <div class="sidebar-brand">
        <a href="{{ route('admin.dashboard') }}" class="brand-link">
            <span class="brand-text fw-light"><b>KIRANA</b> GROUP</span>
        </a>
    </div>
    <!--end::Sidebar Brand-->

    <!--begin::Sidebar Wrapper-->
    <div class="sidebar-wrapper">
        <nav class="mt-2">
            <ul class="nav sidebar-menu flex-column" data-lte-toggle="treeview" role="menu" data-accordion="false">

                <li class="nav-item">
                    <a href="{{ route('admin.dashboard') }}"
                        class="nav-link {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
                        <i class="nav-icon bi bi-speedometer2"></i>
                        <p>Dashboard</p>
                    </a>
                </li>

                <li class="nav-header">MASTER DATA</li>

                <li class="nav-item">
                    <a href="{{ route('admin.employees.index') }}"
                        class="nav-link {{ request()->routeIs('admin.employees.*') ? 'active' : '' }}">
                        <i class="nav-icon bi bi-people-fill"></i>
                        <p>Data Karyawan</p>
                    </a>
                </li>

                {{-- <li class="nav-item">
                    <a href="#" class="nav-link">
                        <i class="nav-icon bi bi-diagram-3-fill"></i>
                        <p>Departemen / Tim</p>
                    </a>
                </li> --}}

                <li class="nav-item">
                    <a href="{{ route('admin.shifts.index') }}"
                        class="nav-link {{ request()->routeIs('admin.shifts.*') ? 'active' : '' }}">
                        <i class="nav-icon bi bi-clock-history"></i>
                        <p>Pengaturan Shift</p>
                    </a>
                </li>

                <li class="nav-item">
                    <a href="{{ route('admin.locations.index') }}" class="nav-link {{ request()->routeIs('admin.locations.*') ? 'active' : '' }}">
                        <i class="nav-icon bi bi-geo-alt-fill"></i>
                        <p>Lokasi Kerja (GPS)</p>
                    </a>
                </li>

                <li class="nav-header">MANAJEMEN KEHADIRAN</li>

                <li class="nav-item">
                    <a href="{{ route('admin.attendances.index') }}"
                        class="nav-link {{ request()->routeIs('admin.attendances.*') ? 'active' : '' }}">
                        <i class="nav-icon bi bi-calendar-check-fill"></i>
                        <p>Riwayat Presensi</p>
                    </a>
                </li>

                {{-- <li class="nav-header">PUSAT PENGAJUAN</li>

                <li class="nav-item">
                    <a href="#" class="nav-link">
                        <i class="nav-icon bi bi-envelope-paper-fill"></i>
                        <p>Pengajuan Cuti</p>
                    </a>
                </li>

                <li class="nav-item">
                    <a href="#" class="nav-link">
                        <i class="nav-icon bi bi-briefcase-fill"></i>
                        <p>Lembur & Dinas</p>
                    </a>
                </li> --}}

                @can('manage-rbac')
                <li class="nav-header">SISTEM</li>

                <li class="nav-item">
                    <a href="{{ route('admin.roles.index') }}"
                        class="nav-link {{ request()->routeIs('admin.roles.*') ? 'active' : '' }}">
                        <i class="nav-icon bi bi-shield-lock-fill"></i>
                        <p>Hak Akses (RBAC)</p>
                    </a>
                </li>
                @endcan

            </ul>
        </nav>
    </div>
    <!--end::Sidebar Wrapper-->
</aside>