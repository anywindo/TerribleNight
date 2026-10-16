<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <title>Sistem Presensi Sedang Pemeliharaan - Kirana People</title>
    <link rel="icon" type="image/jpeg" href="{{ asset('logoKiranaSquare.jpeg') }}">
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap');
        body {
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif;
            background-color: #F6F7FA;
            color: #1C1C1E;
            -webkit-tap-highlight-color: transparent;
        }
    </style>
</head>
<body class="min-h-screen flex items-center justify-center p-4 antialiased select-none">
    <div class="w-full max-w-sm flex flex-col items-center text-center">
        <!-- Brand Header -->
        <div class="flex items-center gap-2 mb-6">
            <img src="{{ asset('logoKiranaSquare.jpeg') }}" alt="Kirana People" class="w-8 h-8 rounded-xl shadow-xs object-cover">
            <span class="text-xs font-black tracking-wider text-[#1C1C1E] uppercase">KIRANA PEOPLE</span>
        </div>

        <!-- Main Card -->
        <div class="w-full bg-white rounded-3xl shadow-[0_8px_30px_rgba(0,0,0,0.06)] border border-gray-100/90 p-7 flex flex-col items-center">
            <!-- Icon Illustration -->
            <div class="relative w-20 h-20 mb-5 flex items-center justify-center">
                <div class="absolute inset-0 bg-pink-100/70 rounded-full animate-ping opacity-30"></div>
                <div class="w-20 h-20 bg-gradient-to-tr from-[#FFF0F7] to-[#FFE2F0] rounded-full flex items-center justify-center shadow-inner border border-pink-100">
                    <i class="fa-solid fa-screwdriver-wrench text-[#FF21A4] text-2xl"></i>
                </div>
            </div>

            <!-- Maintenance Status Badge -->
            <div class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-amber-50 border border-amber-200/60 text-amber-700 text-[11px] font-bold mb-3">
                <span class="w-2 h-2 rounded-full bg-amber-500 animate-pulse"></span>
                <span>Sedang Dalam Pemeliharaan</span>
            </div>

            <!-- Heading -->
            <h1 class="text-lg font-extrabold text-[#1C1C1E] tracking-tight mb-2">
                Presensi Web Diperbarui
            </h1>

            <!-- Description -->
            <p class="text-xs text-[#8E8E93] leading-relaxed mb-6">
                Fitur presensi melalui peramban web saat ini sedang dalam peningkatan sistem. Silakan gunakan <strong class="text-gray-700">Aplikasi Mobile Kirana</strong> untuk melakukan presensi harian Anda.
            </p>

            <!-- Info Box -->
            <div class="w-full bg-[#F6F7FA] rounded-2xl p-3.5 mb-6 text-left border border-gray-100">
                <div class="flex items-start gap-2.5">
                    <div class="w-6 h-6 rounded-full bg-[#FFF0F7] text-[#FF21A4] flex items-center justify-center shrink-0 mt-0.5">
                        <i class="fa-solid fa-mobile-screen text-xs"></i>
                    </div>
                    <div class="flex-1 min-w-0">
                        <p class="text-[12px] font-bold text-[#1C1C1E] leading-tight">Presensi via Mobile Aktif</p>
                        <p class="text-[11px] text-[#8E8E93] leading-normal mt-0.5">Aplikasi Kirana iOS & Android tetap beroperasi normal seperti biasa.</p>
                    </div>
                </div>
            </div>

            <!-- Actions -->
            <div class="w-full space-y-2.5">
                <button type="button" onclick="window.location.reload()"
                    class="w-full bg-[#FF21A4] hover:bg-[#E00084] text-white font-bold py-3.5 rounded-2xl shadow-sm hover:shadow-md transition active:scale-[0.98] text-xs flex items-center justify-center gap-2">
                    <i class="fa-solid fa-rotate-right text-xs"></i>
                    <span>Muat Ulang Halaman</span>
                </button>

                @auth
                <form action="{{ route('logout') }}" method="POST" class="w-full">
                    @csrf
                    <button type="submit"
                        class="w-full bg-white hover:bg-rose-50 border border-rose-200 text-rose-600 font-bold py-3.5 rounded-2xl transition active:scale-[0.98] text-xs flex items-center justify-center gap-2 shadow-2xs">
                        <i class="fa-solid fa-arrow-right-from-bracket text-xs"></i>
                        <span>Keluar Akun</span>
                    </button>
                </form>
                @else
                <a href="{{ route('login') }}"
                    class="w-full bg-white hover:bg-gray-50 border border-gray-200 text-gray-700 font-bold py-3.5 rounded-2xl transition active:scale-[0.98] text-xs flex items-center justify-center gap-2 shadow-2xs block text-center">
                    <i class="fa-solid fa-arrow-left text-xs"></i>
                    <span>Kembali ke Halaman Masuk</span>
                </a>
                @endauth
            </div>
        </div>

        <!-- Footer -->
        <p class="text-[11px] text-[#8E8E93] mt-6">
            &copy; {{ date('Y') }} Kirana People. Semua hak cipta dilindungi.
        </p>
    </div>
</body>
</html>
