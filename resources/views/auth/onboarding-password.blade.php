<!DOCTYPE html>
<html lang="id">

<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
    <title>Aktivasi Akun & Ubah Kata Sandi | Kirana People</title>
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
        .check-item {
            font-size: 0.85rem;
            transition: color 0.2s;
        }

        .check-item.valid {
            color: #198754;
            font-weight: 600;
        }

        .check-item.invalid {
            color: #6c757d;
        }

        .btn-toggle-pwd {
            cursor: pointer;
            border-left: 0;
            background: #fff;
        }
    </style>
</head>

<body class="login-page bg-body-secondary">
    <div class="login-box" style="width: 450px; max-width: 90vw;">
        <div class="login-logo mb-4">
            <a href="/"><b>KIRANA</b> GROUP</a>
        </div>

        <div class="text-center mb-4">
            <h5 class="mb-1">
                Selamat datang, <b>{{ $user->name }}</b>
                @if($user->employee_id)
                    <span class="text-secondary"><br>{{ $user->employee_id }}</span>
                @endif
            </h5>
            <p class="text-muted small">Silakan ubah kata sandi default Anda untuk melanjutkan</p>
        </div>

        <div class="card shadow-sm border-0">
            <div class="card-body p-4">
                <div class="alert alert-warning py-2 px-3 mb-3 small d-flex align-items-start" role="alert">
                    <i class="bi bi-exclamation-triangle-fill flex-shrink-0 me-2 fs-5 text-warning"></i>
                    <div>
                        Akun Anda saat ini menggunakan <strong>kata sandi bawaan (tanggal lahir)</strong>. Silakan buat
                        kata sandi baru.
                    </div>
                </div>

                @if ($errors->any())
                    <div class="alert alert-danger py-2 px-3 mb-3 small">
                        <ul class="mb-0 ps-3">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <form action="{{ route('onboarding.password.update') }}" method="POST" id="onboardingForm">
                    @csrf

                    <!-- Kata Sandi Baru -->
                    <div class="mb-3">
                        <label class="form-label small fw-semibold text-secondary mb-1">
                            Kata Sandi Baru <span class="text-danger">*</span>
                        </label>
                        <div class="input-group">
                            <span class="input-group-text bg-white text-muted">
                                <i class="bi bi-key-fill"></i>
                            </span>
                            <input type="password" name="password" id="passwordInput" class="form-control"
                                placeholder="Minimal 8 karakter" required autofocus>
                            <button type="button" class="input-group-text btn-toggle-pwd" id="togglePasswordBtn">
                                <i class="bi bi-eye" id="passwordIcon"></i>
                            </button>
                        </div>
                    </div>

                    <!-- Konfirmasi Kata Sandi -->
                    <div class="mb-3">
                        <label class="form-label small fw-semibold text-secondary mb-1">
                            Konfirmasi Kata Sandi <span class="text-danger">*</span>
                        </label>
                        <div class="input-group">
                            <span class="input-group-text bg-white text-muted">
                                <i class="bi bi-shield-check"></i>
                            </span>
                            <input type="password" name="password_confirmation" id="confirmPasswordInput"
                                class="form-control" placeholder="Ketik ulang kata sandi baru" required>
                            <button type="button" class="input-group-text btn-toggle-pwd" id="toggleConfirmBtn">
                                <i class="bi bi-eye" id="confirmIcon"></i>
                            </button>
                        </div>
                    </div>

                    <!-- Syarat Password -->
                    <div class="p-2 px-3 rounded bg-light border mb-4">
                        <div class="check-item invalid d-flex align-items-center mb-1" id="ruleLength">
                            <i class="bi bi-circle me-2" id="iconLength"></i>
                            <span>Panjang minimal 8 karakter</span>
                        </div>
                        <div class="check-item invalid d-flex align-items-center mb-1" id="ruleLetters">
                            <i class="bi bi-circle me-2" id="iconLetters"></i>
                            <span>Mengandung huruf besar & kecil (A-Z, a-z)</span>
                        </div>
                        <div class="check-item invalid d-flex align-items-center mb-1" id="ruleNumber">
                            <i class="bi bi-circle me-2" id="iconNumber"></i>
                            <span>Mengandung minimal 1 angka (0-9)</span>
                        </div>
                        <div class="check-item invalid d-flex align-items-center" id="ruleMatch">
                            <i class="bi bi-circle me-2" id="iconMatch"></i>
                            <span>Konfirmasi kata sandi cocok</span>
                        </div>
                    </div>

                    <div class="d-grid gap-2">
                        <button type="submit" class="btn btn-primary py-2 fw-semibold" id="submitBtn" disabled>
                            <span id="submitText">Simpan & Lanjutkan</span>
                            <i class="bi bi-arrow-right ms-1"></i>
                        </button>
                    </div>
                </form>
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

    <!--begin::Scripts-->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"
        crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/admin-lte@4.0.0/dist/js/adminlte.min.js"></script>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const pwdInput = document.getElementById('passwordInput');
            const confirmInput = document.getElementById('confirmPasswordInput');
            const togglePwdBtn = document.getElementById('togglePasswordBtn');
            const toggleConfirmBtn = document.getElementById('toggleConfirmBtn');
            const pwdIcon = document.getElementById('passwordIcon');
            const confirmIcon = document.getElementById('confirmIcon');

            const ruleLength = document.getElementById('ruleLength');
            const ruleLetters = document.getElementById('ruleLetters');
            const ruleNumber = document.getElementById('ruleNumber');
            const ruleMatch = document.getElementById('ruleMatch');

            const iconLength = document.getElementById('iconLength');
            const iconLetters = document.getElementById('iconLetters');
            const iconNumber = document.getElementById('iconNumber');
            const iconMatch = document.getElementById('iconMatch');

            const submitBtn = document.getElementById('submitBtn');
            const onboardingForm = document.getElementById('onboardingForm');
            const submitText = document.getElementById('submitText');

            togglePwdBtn.addEventListener('click', function () {
                const isPassword = pwdInput.type === 'password';
                pwdInput.type = isPassword ? 'text' : 'password';
                pwdIcon.className = isPassword ? 'bi bi-eye-slash' : 'bi bi-eye';
            });

            toggleConfirmBtn.addEventListener('click', function () {
                const isPassword = confirmInput.type === 'password';
                confirmInput.type = isPassword ? 'text' : 'password';
                confirmIcon.className = isPassword ? 'bi bi-eye-slash' : 'bi bi-eye';
            });

            function updateRule(elem, iconElem, isValid) {
                if (isValid) {
                    elem.className = elem.className.replace('invalid', 'valid');
                    iconElem.className = 'bi bi-check-circle-fill me-2 text-success';
                } else {
                    elem.className = elem.className.replace('valid', 'invalid');
                    iconElem.className = 'bi bi-circle me-2';
                }
            }

            function validatePasswords() {
                const val = pwdInput.value;
                const confirmVal = confirmInput.value;

                const isLenValid = val.length >= 8;
                const hasMixedCase = /[a-z]/.test(val) && /[A-Z]/.test(val);
                const hasNumber = /[0-9]/.test(val);
                const isMatchValid = val.length > 0 && val === confirmVal;

                updateRule(ruleLength, iconLength, isLenValid);
                updateRule(ruleLetters, iconLetters, hasMixedCase);
                updateRule(ruleNumber, iconNumber, hasNumber);
                updateRule(ruleMatch, iconMatch, isMatchValid);

                const allValid = isLenValid && hasMixedCase && hasNumber && isMatchValid;
                submitBtn.disabled = !allValid;
            }

            pwdInput.addEventListener('input', validatePasswords);
            confirmInput.addEventListener('input', validatePasswords);

            onboardingForm.addEventListener('submit', function () {
                submitBtn.disabled = true;
                submitText.innerHTML = '<span class="spinner-border spinner-border-sm me-2" role="status" aria-hidden="true"></span>Menyimpan...';
            });
        });
    </script>
    <!--end::Scripts-->
</body>

</html>