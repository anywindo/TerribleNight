<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=0">
    <title>Presensi | TerribleNight</title>
    <!-- Tailwind CSS (CDN for rapid prototyping) -->
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap');

        body {
            font-family: 'Inter', sans-serif;
            background-color: #272933;
            color: #ffffff;
        }

        .card-bg {
            background-color: #313340;
        }
    </style>
</head>

<body class="min-h-screen pb-24" x-data="{ activeTab: 'work', currentNav: 'home' }">
    <!-- Top Header -->
    <header class="px-5 pt-10 pb-4" x-show="currentNav === 'home'">
        <div class="flex justify-between items-center mb-6">
            <h1 class="text-lg font-semibold tracking-wide uppercase">KIRANA GROUP</h1>
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit"
                    class="text-gray-400 hover:text-red-500 transition active:scale-95 flex items-center justify-center w-8 h-8 rounded-full bg-gray-700/50">
                    <i class="fa-solid fa-right-from-bracket text-sm"></i>
                </button>
            </form>
        </div>

        <div class="flex justify-between items-center">
            <div class="flex items-center space-x-3">
                <img src="https://ui-avatars.com/api/?name={{ urlencode($user->name) }}&background=e2e8f0&color=475569"
                    class="w-12 h-12 rounded-full border-2 border-gray-600 bg-white">
                <div>
                    <h2 class="font-bold text-base">{{ $user->name }}</h2>
                    <p class="text-sm text-gray-400">{{ $user->email }}</p>
                </div>
            </div>
        </div>
    </header>

    @if(session('success'))
        <div class="px-5 mb-2">
            <div
                class="bg-green-500/20 border border-green-500/50 text-green-400 px-4 py-2 rounded-lg text-sm flex items-center">
                <i class="fa-solid fa-circle-check mr-2"></i> {{ session('success') }}
            </div>
        </div>
    @endif

    <!-- Tabs -->
    <div class="flex px-5 border-b border-gray-600 mt-2" x-show="currentNav === 'home'">
        <button @click="activeTab = 'work'"
            :class="activeTab === 'work' ? 'text-white border-orange-500' : 'text-gray-400 border-transparent'"
            class="flex-1 pb-3 text-center font-medium border-b-2 transition-colors">
            Work
        </button>
        <button @click="activeTab = 'break'"
            :class="activeTab === 'break' ? 'text-white border-orange-500' : 'text-gray-400 border-transparent'"
            class="flex-1 pb-3 text-center font-medium border-b-2 transition-colors">
            Break
        </button>
    </div>

    <!-- Main Content -->
    <main class="px-5 mt-4" x-show="currentNav === 'home'" x-transition:enter="transition ease-out duration-200"
        x-transition:enter-start="opacity-0 scale-95" x-transition:enter-end="opacity-100 scale-100">
        <!-- Work Tab -->
        <div x-show="activeTab === 'work'" x-transition:enter="transition ease-out duration-200"
            x-transition:enter-start="opacity-0 translate-y-2" x-transition:enter-end="opacity-100 translate-y-0">
            <!-- Card -->
            <div class="card-bg rounded-xl p-4 mb-4">
                <div class="flex justify-between items-start mb-4 border-b border-gray-600 pb-3">
                    <div>
                        <p class="text-xs text-gray-400 mb-1">Today ({{ now()->translatedFormat('D, d M Y') }})</p>
                        <p class="text-sm font-semibold text-gray-200">Shift: OFFICE [08:00 - 16:00]</p>
                    </div>
                    <!-- <i class="fa-solid fa-file-pen text-gray-400 text-lg"></i> -->
                </div>

                <div class="flex mb-5 relative">
                    <!-- Divider line -->
                    <div class="absolute left-1/2 top-0 bottom-0 w-[1px] bg-gray-600"></div>

                    @php
                        $startEvent = $events->where('event_type', 'START_SHIFT')->first();
                        $endEvent = $events->where('event_type', 'END_SHIFT')->first();
                    @endphp

                    <!-- Start Time -->
                    <div class="flex-1 flex items-center">
                        <img src="https://ui-avatars.com/api/?name={{ urlencode($user->name) }}&background=random"
                            class="w-10 h-10 rounded-full mr-3 border border-gray-600">
                        <div>
                            <p class="text-xs text-gray-400 mb-0.5">Start Time</p>
                            @if($startEvent)
                                <p class="text-green-500 font-bold text-lg leading-none">
                                    {{ \Carbon\Carbon::parse($startEvent->timestamp)->format('H:i') }} <i
                                        class="fa-solid fa-circle-check text-xs ml-1"></i>
                                </p>
                            @else
                                <p class="text-red-500 font-bold text-lg leading-none">--:-- <i
                                        class="fa-solid fa-location-dot text-xs ml-1"></i></p>
                            @endif
                        </div>
                    </div>

                    <!-- End Time -->
                    <div class="flex-1 flex items-center pl-4">
                        <div
                            class="w-10 h-10 rounded-full bg-gray-200 text-gray-600 flex items-center justify-center font-bold mr-3">
                            {{ strtoupper(substr($user->name, 0, 2)) }}
                        </div>
                        <div>
                            <p class="text-xs text-gray-400 mb-0.5">End Time</p>
                            @if($endEvent)
                                <p class="text-green-500 font-bold text-lg leading-none">
                                    {{ \Carbon\Carbon::parse($endEvent->timestamp)->format('H:i') }} <i
                                        class="fa-solid fa-circle-check text-xs ml-1"></i>
                                </p>
                            @else
                                <p class="text-red-500 font-bold text-lg leading-none">--:-- <i
                                        class="fa-solid fa-location-dot text-xs ml-1"></i></p>
                            @endif
                        </div>
                    </div>
                </div>

                <form action="{{ route('attendance.store') }}" method="POST" id="attendanceForm"
                    enctype="multipart/form-data">
                    @csrf
                    <input type="hidden" name="latitude" id="lat">
                    <input type="hidden" name="longitude" id="lng">

                    <!-- Camera trigger hidden input -->
                    <input type="file" name="selfie" id="selfie_input" accept="image/*" capture="user" class="hidden"
                        required>

                    @if(!$startEvent)
                        <input type="hidden" name="event_type" value="START_SHIFT">
                        @php
                            $ruleEnabled = \App\Models\Setting::get('attendance_rule_enabled', false);
                            $isPastCutoff = false;
                            $cutoffTime = null;
                            if ($ruleEnabled && $shift->default_start_time) {
                                $ruleMinutes = \App\Models\Setting::get('attendance_rule_minutes', 15);
                                $shiftStartTime = \Carbon\Carbon::parse($shift->default_start_time);
                                $cutoffTime = $shiftStartTime->copy()->subMinutes($ruleMinutes);
                                if (now()->format('H:i:s') >= $cutoffTime->format('H:i:s')) {
                                    $isPastCutoff = true;
                                }
                            }
                        @endphp
                        
                        @if($isPastCutoff)
                            <button type="button" disabled
                                class="w-full bg-red-900/50 text-red-400 border border-red-500 font-bold py-3 rounded-lg flex flex-col items-center justify-center cursor-not-allowed">
                                <span>Record Time</span>
                                <span class="text-[10px] font-normal mt-0.5">Batas waktu habis (sebelum {{ $cutoffTime->format('H:i') }})</span>
                            </button>
                        @else
                            <button type="button" onclick="document.getElementById('selfie_input').click()"
                                class="w-full bg-[#f97316] hover:bg-[#ea580c] text-white font-bold py-3 rounded-lg transition active:scale-95 shadow-md">
                                Record Time
                            </button>
                        @endif
                    @elseif(!$endEvent)
                        @php
                            $hasActiveBreak = $events->contains('event_type', 'START_BREAK') && !$events->contains('event_type', 'END_BREAK');
                        @endphp
                        <input type="hidden" name="event_type" value="END_SHIFT">
                        @if($hasActiveBreak)
                            <button type="button" disabled
                                class="w-full bg-gray-600 text-gray-400 font-bold py-3 rounded-lg flex flex-col items-center justify-center cursor-not-allowed">
                                <span>Record End Time</span>
                                <span class="text-[10px] font-normal mt-0.5">Selesaikan istirahat terlebih dahulu</span>
                            </button>
                        @else
                            <button type="button" onclick="document.getElementById('selfie_input').click()"
                                class="w-full bg-[#f97316] hover:bg-[#ea580c] text-white font-bold py-3 rounded-lg transition active:scale-95 shadow-md">
                                Record End Time
                            </button>
                        @endif
                    @else
                        <button type="button" disabled class="w-full bg-gray-600 text-gray-400 font-bold py-3 rounded-lg cursor-not-allowed">
                            Shift Completed
                        </button>
                    @endif
                </form>
                <div id="locationStatus" class="text-xs text-center mt-3 text-gray-400">
                    <i class="fa-solid fa-spinner fa-spin"></i> Getting GPS Location...
                </div>
            </div>

            <!-- Attendance History -->
            <div class="mt-6">
                <h3 class="text-xs font-bold text-gray-400 uppercase tracking-wide mb-3">Attendance History</h3>

                <div class="space-y-3">
                    @forelse($historyEvents->filter(fn($e) => in_array($e->event_type?->value ?? $e->event_type, ['START_SHIFT', 'END_SHIFT'])) as $event)
                        <div class="card-bg rounded-xl p-4 flex items-center justify-between border border-gray-700">
                            <div class="flex items-center space-x-4">
                                @if($event->selfie_path && $event->selfie_path !== 'dummy/path.jpg')
                                    <img src="{{ asset('storage/' . $event->selfie_path) }}"
                                        class="w-10 h-10 rounded-full object-cover border border-gray-600">
                                @else
                                    <div
                                        class="w-10 h-10 rounded-full flex items-center justify-center 
                                                    {{ $event->event_type === 'START_SHIFT' ? 'bg-green-900/30 text-green-500' : 'bg-red-900/30 text-red-500' }}">
                                        <i
                                            class="fa-solid {{ $event->event_type === 'START_SHIFT' ? 'fa-right-to-bracket' : 'fa-right-from-bracket' }}"></i>
                                    </div>
                                @endif
                                <div>
                                    <p class="text-sm font-bold text-gray-200">
                                        {{ $event->event_type === 'START_SHIFT' ? 'Start Shift' : 'End Shift' }}
                                    </p>
                                    <p class="text-xs text-gray-400">
                                        {{ \Carbon\Carbon::parse($event->timestamp)->translatedFormat('d M Y') }}
                                    </p>
                                </div>
                            </div>
                            <div class="text-right">
                                <p class="text-lg font-bold text-white">
                                    {{ \Carbon\Carbon::parse($event->timestamp)->format('H:i') }}
                                </p>
                            </div>
                        </div>
                    @empty
                        <div class="text-center py-6 text-gray-500 border border-dashed border-gray-700 rounded-xl">
                            <i class="fa-solid fa-clock-rotate-left text-2xl mb-2 opacity-50"></i>
                            <p class="text-xs font-medium">No history yet</p>
                        </div>
                    @endforelse
                </div>
            </div>
        </div>

        <!-- Break Tab -->
        <div x-show="activeTab === 'break'" style="display: none;" x-transition:enter="transition ease-out duration-200"
            x-transition:enter-start="opacity-0 translate-y-2" x-transition:enter-end="opacity-100 translate-y-0">
            <!-- Break Card -->
            <div class="card-bg rounded-xl p-4 mb-6">
                <div class="mb-4">
                    <p class="text-xs text-gray-400 mb-2">{{ now()->translatedFormat('D, d M Y') }}</p>
                    <div class="flex items-center">
                        <p class="text-xs font-semibold text-gray-400 uppercase tracking-wide mr-3">
                            Break Time [{{ $shift->break_start ? \Carbon\Carbon::parse($shift->break_start)->format('H:i') : '--:--' }} - {{ $shift->break_end ? \Carbon\Carbon::parse($shift->break_end)->format('H:i') : '--:--' }}]
                        </p>
                        <div class="flex-1 h-[1px] bg-gray-600"></div>
                    </div>
                </div>

                <div class="flex mb-6 relative mt-4">
                    <!-- Divider line -->
                    <div class="absolute left-1/2 top-0 bottom-0 w-[1px] bg-gray-600"></div>

                    @php
                        $startBreak = $events->where('event_type', 'START_BREAK')->first();
                        $endBreak = $events->where('event_type', 'END_BREAK')->first();
                    @endphp

                    <!-- Start Break -->
                    <div class="flex-1 flex items-center">
                        <img src="https://ui-avatars.com/api/?name={{ urlencode($user->name) }}&background=random"
                            class="w-10 h-10 rounded-full mr-3 border border-gray-600">
                        <div>
                            <p class="text-xs text-gray-400 mb-0.5">Start Break</p>
                            @if($startBreak)
                                <p class="text-white font-bold text-lg leading-none">
                                    {{ \Carbon\Carbon::parse($startBreak->timestamp)->format('H:i') }}
                                </p>
                            @else
                                <p class="text-white font-bold text-lg leading-none">--:--</p>
                            @endif
                        </div>
                    </div>

                    <!-- End Break -->
                    <div class="flex-1 flex items-center pl-4">
                        <img src="https://ui-avatars.com/api/?name={{ urlencode($user->name) }}&background=random"
                            class="w-10 h-10 rounded-full mr-3 border border-gray-600">
                        <div>
                            <p class="text-xs text-gray-400 mb-0.5">End Break</p>
                            @if($endBreak)
                                <p class="text-white font-bold text-lg leading-none">
                                    {{ \Carbon\Carbon::parse($endBreak->timestamp)->format('H:i') }}
                                </p>
                            @else
                                <p class="text-white font-bold text-lg leading-none">--:--</p>
                            @endif
                        </div>
                    </div>
                </div>

                <form action="{{ route('attendance.store') }}" method="POST" id="breakForm"
                    enctype="multipart/form-data">
                    @csrf
                    <input type="hidden" name="latitude" id="lat2">
                    <input type="hidden" name="longitude" id="lng2">
                    <input type="file" name="selfie" id="break_selfie_input" accept="image/*" capture="user"
                        class="hidden" required>

                    <div class="flex space-x-3">
                        @php
                            $nowTime = now()->format('H:i:s');
                            $breakStartTime = $shift->break_start ? \Carbon\Carbon::parse($shift->break_start)->format('H:i:s') : null;
                            $breakEndTime = $shift->break_end ? \Carbon\Carbon::parse($shift->break_end)->format('H:i:s') : null;
                            $shiftNotStarted = !$events->contains('event_type', 'START_SHIFT');
                        @endphp
                        @if(!$startBreak)
                            <input type="hidden" name="event_type" value="START_BREAK">
                            @if($shiftNotStarted)
                                <button type="button" disabled
                                    class="flex-1 bg-gray-600 text-gray-400 font-bold py-3 rounded-lg flex flex-col items-center justify-center cursor-not-allowed">
                                    <span>Record Break Time</span>
                                    <span class="text-[10px] font-normal mt-0.5">Mulai shift terlebih dahulu</span>
                                </button>
                            @elseif($breakStartTime && $nowTime < $breakStartTime)
                                <button type="button" disabled
                                    class="flex-1 bg-gray-600 text-gray-400 font-bold py-3 rounded-lg flex flex-col items-center justify-center cursor-not-allowed">
                                    <span>Record Break Time</span>
                                    <span class="text-[10px] font-normal mt-0.5">Belum waktunya istirahat</span>
                                </button>
                            @else
                                <button type="button" onclick="document.getElementById('break_selfie_input').click()"
                                    class="flex-1 bg-[#f97316] hover:bg-[#ea580c] text-white font-bold py-3 rounded-lg transition active:scale-95 shadow-md">
                                    Record Break Time
                                </button>
                            @endif
                        @elseif(!$endBreak)
                            <input type="hidden" name="event_type" value="END_BREAK">
                            @if($breakEndTime && $nowTime > $breakEndTime)
                                <button type="button" disabled
                                    class="flex-1 bg-red-900/50 text-red-400 border border-red-500 font-bold py-3 rounded-lg flex flex-col items-center justify-center cursor-not-allowed">
                                    <span>Record End Break</span>
                                    <span class="text-[10px] font-normal mt-0.5">Waktu istirahat telah berakhir</span>
                                </button>
                            @else
                                <button type="button" onclick="document.getElementById('break_selfie_input').click()"
                                    class="flex-1 bg-[#f97316] hover:bg-[#ea580c] text-white font-bold py-3 rounded-lg transition active:scale-95 shadow-md">
                                    Record End Break
                                </button>
                            @endif
                        @else
                            <button type="button" disabled
                                class="flex-1 bg-gray-600 text-gray-400 font-bold py-3 rounded-lg cursor-not-allowed">
                                Break Completed
                            </button>
                        @endif
                    </div>
                </form>
            </div>

            <!-- Break History -->
            <div class="mt-6">
                <h3 class="text-xs font-bold text-gray-400 uppercase tracking-wide mb-3">Break History</h3>

                <div class="space-y-3">
                    @forelse($historyEvents->filter(fn($e) => in_array($e->event_type?->value ?? $e->event_type, ['START_BREAK', 'END_BREAK'])) as $event)
                        <div class="card-bg rounded-xl p-4 flex items-center justify-between border border-gray-700">
                            <div class="flex items-center space-x-4">
                                @if($event->selfie_path && $event->selfie_path !== 'dummy/path.jpg')
                                    <img src="{{ asset('storage/' . $event->selfie_path) }}"
                                        class="w-10 h-10 rounded-full object-cover border border-gray-600">
                                @else
                                    <div
                                        class="w-10 h-10 rounded-full flex items-center justify-center 
                                                    {{ $event->event_type === 'START_BREAK' ? 'bg-orange-900/30 text-orange-500' : 'bg-blue-900/30 text-blue-500' }}">
                                        <i
                                            class="fa-solid {{ $event->event_type === 'START_BREAK' ? 'fa-mug-hot' : 'fa-briefcase' }}"></i>
                                    </div>
                                @endif
                                <div>
                                    <p class="text-sm font-bold text-gray-200">
                                        {{ $event->event_type === 'START_BREAK' ? 'Start Break' : 'End Break' }}
                                    </p>
                                    <p class="text-xs text-gray-400">
                                        {{ \Carbon\Carbon::parse($event->timestamp)->translatedFormat('d M Y') }}
                                    </p>
                                </div>
                            </div>
                            <div class="text-right">
                                <p class="text-lg font-bold text-white">
                                    {{ \Carbon\Carbon::parse($event->timestamp)->format('H:i') }}
                                </p>
                            </div>
                        </div>
                    @empty
                        <div class="text-center py-6 text-gray-500 border border-dashed border-gray-700 rounded-xl">
                            <i class="fa-solid fa-clock-rotate-left text-2xl mb-2 opacity-50"></i>
                            <p class="text-xs font-medium">No break history yet</p>
                        </div>
                    @endforelse
                </div>
            </div>

        </div>
    </main>

    <!-- Profile Content -->
    <main class="px-5 pt-12 mt-4" x-show="currentNav === 'profile'" style="display: none;"
        x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 scale-95"
        x-transition:enter-end="opacity-100 scale-100">
        <h1 class="text-2xl font-bold mb-6">Profile</h1>

        <div class="card-bg rounded-xl p-6 text-center mb-6">
            <img src="https://ui-avatars.com/api/?name={{ urlencode($user->name) }}&background=e2e8f0&color=475569&size=128"
                class="w-24 h-24 rounded-full border-4 border-gray-600 bg-white mx-auto mb-4">
            <h2 class="text-xl font-bold text-gray-100">{{ $user->name }}</h2>

            <div class="mt-4 flex justify-center space-x-2">
                <span
                    class="px-3 py-1 bg-blue-900/40 text-blue-400 border border-blue-800/50 rounded-full text-[10px] font-semibold uppercase tracking-wide">Employee</span>
                @if($user->isAdmin())
                    <span
                        class="px-3 py-1 bg-orange-900/40 text-orange-400 border border-orange-800/50 rounded-full text-[10px] font-semibold uppercase tracking-wide">Admin/HR</span>
                @endif
            </div>
        </div>

        <div class="card-bg rounded-xl p-5 mb-6 space-y-4">
            <div>
                <p class="text-xs text-gray-500 mb-1">Full Name</p>
                <p class="font-medium text-gray-200">{{ $user->name }}</p>
            </div>
            <div class="h-[1px] bg-gray-700"></div>
            <div>
                <p class="text-xs text-gray-500 mb-1">Email Address</p>
                <p class="font-medium text-gray-200">{{ $user->email }}</p>
            </div>
            <div class="h-[1px] bg-gray-700"></div>
            <div>
                <p class="text-xs text-gray-500 mb-1">Employee ID (NIK)</p>
                <p class="font-medium text-gray-200">{{ $user->nik ?? 'Not set' }}</p>
            </div>
            <div class="h-[1px] bg-gray-700"></div>
            <div>
                <p class="text-xs text-gray-500 mb-1">Registered Since</p>
                <p class="font-medium text-gray-200">
                    {{ $user->created_at ? $user->created_at->translatedFormat('d M Y') : '-' }}
                </p>
            </div>
        </div>

        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button type="submit"
                class="w-full card-bg hover:bg-red-900/30 border border-gray-700 hover:border-red-800/50 text-red-500 font-bold py-4 rounded-xl transition flex items-center justify-center">
                <i class="fa-solid fa-right-from-bracket mr-2 text-lg"></i> Sign Out
            </button>
        </form>
    </main>

    <!-- Bottom Navigation -->
    <nav
        class="fixed bottom-0 w-full card-bg border-t border-gray-700 flex justify-around items-center px-6 pt-3 pb-6 z-50">
        <div @click="currentNav = 'home'"
            :class="currentNav === 'home' ? 'text-orange-500' : 'text-gray-400 hover:text-gray-200'"
            class="flex flex-col items-center cursor-pointer w-16 transition">
            <i class="fa-solid fa-house text-xl mb-1"></i>
            <span class="text-[10px] font-semibold">Home</span>
        </div>
        <div @click="currentNav = 'profile'"
            :class="currentNav === 'profile' ? 'text-orange-500' : 'text-gray-400 hover:text-gray-200'"
            class="flex flex-col items-center cursor-pointer transition w-16">
            <div class="w-6 h-6 rounded-full overflow-hidden border mb-1 transition-colors"
                :class="currentNav === 'profile' ? 'border-orange-500' : 'border-gray-500'">
                <img src="https://ui-avatars.com/api/?name={{ urlencode($user->name) }}&background=random"
                    class="w-full h-full object-cover">
            </div>
            <span class="text-[10px] font-medium">Profile</span>
        </div>
    </nav>

    <!-- Trigger form submit when file is selected -->
    <script>
        document.getElementById('selfie_input').addEventListener('change', function () {
            if (this.files && this.files.length > 0) {
                // Show loading state
                const btn = this.closest('form').querySelector('button[type="button"]');
                const originalText = btn.innerHTML;
                btn.innerHTML = '<i class="fa-solid fa-spinner fa-spin mr-2"></i> Processing...';
                btn.disabled = true;

                document.getElementById('attendanceForm').submit();
            }
        });

        document.getElementById('break_selfie_input').addEventListener('change', function () {
            if (this.files && this.files.length > 0) {
                // Show loading state
                const btn = this.closest('form').querySelector('button[type="button"]');
                const originalText = btn.innerHTML;
                btn.innerHTML = '<i class="fa-solid fa-spinner fa-spin mr-2"></i> Processing...';
                btn.disabled = true;

                document.getElementById('breakForm').submit();
            }
        });

        // Geolocation
        document.addEventListener("DOMContentLoaded", function () {
            if ("geolocation" in navigator) {
                navigator.geolocation.getCurrentPosition(function (position) {
                    document.getElementById('lat').value = position.coords.latitude;
                    document.getElementById('lng').value = position.coords.longitude;
                    document.getElementById('lat2').value = position.coords.latitude;
                    document.getElementById('lng2').value = position.coords.longitude;

                    document.getElementById('locationStatus').innerHTML = '<i class="fa-solid fa-location-dot text-green-500 mr-1"></i> GPS Ready';
                }, function (error) {
                    document.getElementById('locationStatus').innerHTML = '<i class="fa-solid fa-triangle-exclamation text-red-500 mr-1"></i> Error Getting GPS Location';
                    document.getElementById('locationStatus').classList.add('text-red-400');
                }, {
                    enableHighAccuracy: true,
                    timeout: 10000,
                    maximumAge: 0
                });
            } else {
                document.getElementById('locationStatus').innerHTML = 'Browser does not support GPS';
            }
        });
    </script>
</body>

</html>