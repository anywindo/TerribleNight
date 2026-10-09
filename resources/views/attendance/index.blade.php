<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=0">
    <title>Presensi | SIMSDM Garment</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY=" crossorigin="" />
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js" integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo=" crossorigin=""></script>
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
        .leaflet-bottom, .leaflet-top {
            z-index: 40 !important;
        }
    </style>
</head>

<body class="min-h-screen pb-24"
    x-data="{ 
        activeTab: 'work', 
        currentNav: '{{ request()->hasAny(['start_date', 'end_date']) ? 'history' : 'home' }}',
        showPreview: false,
        showCamera: false,
        previewImage: null,
        currentFormId: null,
        locationData: { lat: null, lng: null, accuracy: null, distance: null },
        locationStatusText: 'Getting location...',
        locationValid: false,
        showNotes: false,
        isSubmitting: false
    }"
    x-init="window.alpineApp = $data"
    @preview-ready.window="previewImage = $event.detail.image; currentFormId = $event.detail.formId; showPreview = true; setTimeout(() => getLocationAndInitMap(), 50)">
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
                <img src="{{ $user->avatar && Storage::disk('public')->exists($user->avatar) ? Storage::url($user->avatar) : asset('userdefault-160x160.jpg') }}"
                    class="w-12 h-12 rounded-full border-2 border-gray-600 bg-white object-cover">
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
                        @if($startEvent && $startEvent->selfie_path && $startEvent->selfie_path !== 'dummy/path.jpg')
                            <img src="{{ asset('storage/' . $startEvent->selfie_path) }}"
                                class="w-10 h-10 rounded-full mr-3 border border-gray-600 object-cover">
                        @else
                            <div
                                class="w-10 h-10 rounded-full bg-orange-900/30 text-orange-500 flex items-center justify-center font-bold mr-3 border border-orange-800/50">
                                <i class="fa-solid fa-right-to-bracket text-sm"></i>
                            </div>
                        @endif
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
                        @if($endEvent && $endEvent->selfie_path && $endEvent->selfie_path !== 'dummy/path.jpg')
                            <img src="{{ asset('storage/' . $endEvent->selfie_path) }}"
                                class="w-10 h-10 rounded-full mr-3 border border-gray-600 object-cover">
                        @else
                            <div
                                class="w-10 h-10 rounded-full bg-red-900/30 text-red-500 flex items-center justify-center font-bold mr-3 border border-red-800/50">
                                <i class="fa-solid fa-right-from-bracket text-sm"></i>
                            </div>
                        @endif
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
                        <button type="button" onclick="openCameraUI('attendanceForm')"
                            class="w-full bg-[#f97316] hover:bg-[#ea580c] text-white font-bold py-3 rounded-lg transition active:scale-95 shadow-md">
                            Record Time
                        </button>
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
                            <button type="button" onclick="openCameraUI('attendanceForm')"
                                class="w-full bg-[#f97316] hover:bg-[#ea580c] text-white font-bold py-3 rounded-lg transition active:scale-95 shadow-md">
                                Record End Time
                            </button>
                        @endif
                    @else
                        <button type="button" disabled
                            class="w-full bg-gray-600 text-gray-400 font-bold py-3 rounded-lg cursor-not-allowed">
                            Shift Completed
                        </button>
                    @endif
                </form>

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
                                                                    {{ ($event->event_type?->value ?? $event->event_type) === 'START_SHIFT' ? 'bg-green-900/30 text-green-500' : 'bg-red-900/30 text-red-500' }}">
                                        <i
                                            class="fa-solid {{ ($event->event_type?->value ?? $event->event_type) === 'START_SHIFT' ? 'fa-right-to-bracket' : 'fa-right-from-bracket' }}"></i>
                                    </div>
                                @endif
                                <div>
                                    <p class="text-sm font-bold text-gray-200">
                                        {{ ($event->event_type?->value ?? $event->event_type) === 'START_SHIFT' ? 'Start Shift' : 'End Shift' }}
                                    </p>
                                    <p class="text-xs text-gray-400">
                                        {{ \Carbon\Carbon::parse($event->timestamp)->translatedFormat('d M Y') }}
                                        @if($event->latitude && $event->longitude)
                                            | <a href="https://maps.google.com/?q={{ $event->latitude }},{{ $event->longitude }}"
                                                target="_blank" class="text-blue-400 hover:text-blue-300 hover:underline"><i
                                                    class="fa-solid fa-map-location-dot"></i> Maps</a>
                                        @endif
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
                    @php
                        $displayBreakStart = ($shift && $shift->break_start) ? \Carbon\Carbon::parse($shift->break_start)->format('H:i') : '--:--';
                        $displayBreakEnd = ($shift && $shift->break_end) ? \Carbon\Carbon::parse($shift->break_end)->format('H:i') : '--:--';
                        if (now()->isFriday() && $shift && $shift->break_start) {
                            $displayBreakStart = '11:30';
                            $displayBreakEnd = '13:00';
                        }
                    @endphp
                    <div class="flex items-center">
                        <p class="text-xs font-semibold text-gray-400 uppercase tracking-wide mr-3">
                            Break Time
                            [{{ $displayBreakStart }} - {{ $displayBreakEnd }}]
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
                        @if($startBreak && $startBreak->selfie_path && $startBreak->selfie_path !== 'dummy/path.jpg')
                            <img src="{{ asset('storage/' . $startBreak->selfie_path) }}"
                                class="w-10 h-10 rounded-full mr-3 border border-gray-600 object-cover">
                        @else
                            <div
                                class="w-10 h-10 rounded-full bg-orange-900/30 text-orange-500 flex items-center justify-center font-bold mr-3 border border-orange-800/50">
                                <i class="fa-solid fa-mug-hot text-sm"></i>
                            </div>
                        @endif
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
                        @if($endBreak && $endBreak->selfie_path && $endBreak->selfie_path !== 'dummy/path.jpg')
                            <img src="{{ asset('storage/' . $endBreak->selfie_path) }}"
                                class="w-10 h-10 rounded-full mr-3 border border-gray-600 object-cover">
                        @else
                            <div
                                class="w-10 h-10 rounded-full bg-blue-900/30 text-blue-500 flex items-center justify-center font-bold mr-3 border border-blue-800/50">
                                <i class="fa-solid fa-briefcase text-sm"></i>
                            </div>
                        @endif
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
                            $shiftNotStarted = !$events->contains('event_type', 'START_SHIFT');
                            $shiftEnded = $events->contains('event_type', 'END_SHIFT');
                        @endphp

                        @if($shiftEnded)
                            <button type="button" disabled
                                class="flex-1 bg-gray-600 text-gray-400 font-bold py-3 rounded-lg flex flex-col items-center justify-center cursor-not-allowed">
                                <span>Record Break Time</span>
                                <span class="text-[10px] font-normal mt-0.5">Shift telah selesai</span>
                            </button>
                        @elseif(!$startBreak)
                            <input type="hidden" name="event_type" value="START_BREAK">
                            @if($shiftNotStarted)
                                <button type="button" disabled
                                    class="flex-1 bg-gray-600 text-gray-400 font-bold py-3 rounded-lg flex flex-col items-center justify-center cursor-not-allowed">
                                    <span>Record Break Time</span>
                                    <span class="text-[10px] font-normal mt-0.5">Mulai shift terlebih dahulu</span>
                                </button>
                            @else
                                <button type="button" onclick="openCameraUI('breakForm')"
                                    class="flex-1 bg-[#f97316] hover:bg-[#ea580c] text-white font-bold py-3 rounded-lg transition active:scale-95 shadow-md">
                                    Record Break Time
                                </button>
                            @endif
                        @elseif(!$endBreak)
                            <input type="hidden" name="event_type" value="END_BREAK">
                            <button type="button" onclick="openCameraUI('breakForm')"
                                class="flex-1 bg-[#f97316] hover:bg-[#ea580c] text-white font-bold py-3 rounded-lg transition active:scale-95 shadow-md">
                                Record End Break
                            </button>
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
                                                                    {{ ($event->event_type?->value ?? $event->event_type) === 'START_BREAK' ? 'bg-orange-900/30 text-orange-500' : 'bg-blue-900/30 text-blue-500' }}">
                                        <i
                                            class="fa-solid {{ ($event->event_type?->value ?? $event->event_type) === 'START_BREAK' ? 'fa-mug-hot' : 'fa-briefcase' }}"></i>
                                    </div>
                                @endif
                                <div>
                                    <p class="text-sm font-bold text-gray-200">
                                        {{ ($event->event_type?->value ?? $event->event_type) === 'START_BREAK' ? 'Start Break' : 'End Break' }}
                                    </p>
                                    <p class="text-xs text-gray-400">
                                        {{ \Carbon\Carbon::parse($event->timestamp)->translatedFormat('d M Y') }}
                                        @if($event->latitude && $event->longitude)
                                            | <a href="https://maps.google.com/?q={{ $event->latitude }},{{ $event->longitude }}"
                                                target="_blank" class="text-blue-400 hover:text-blue-300 hover:underline"><i
                                                    class="fa-solid fa-map-location-dot"></i> Maps</a>
                                        @endif
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

    <!-- History Content -->
    <main class="px-5 pt-12 mt-4 pb-24" x-show="currentNav === 'history'" style="display: none;"
        x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 scale-95"
        x-transition:enter-end="opacity-100 scale-100">
        <h1 class="text-2xl font-bold mb-4 text-gray-100">Attendance History</h1>

        <div x-data="{ showFilters: {{ request()->hasAny(['start_date', 'end_date']) ? 'true' : 'false' }} }" class="mb-6">
            <button @click="showFilters = !showFilters" type="button" class="flex items-center space-x-2 text-sm text-gray-300 bg-gray-800 hover:bg-gray-700 px-3 py-2 rounded-lg border border-gray-700 transition">
                <i class="fa-solid fa-filter"></i>
                <span>Filter History</span>
                <i class="fa-solid fa-chevron-down text-xs transition-transform duration-200" :class="showFilters ? 'rotate-180' : ''"></i>
            </button>

            <div x-show="showFilters" style="display: none;" 
                 x-transition:enter="transition ease-out duration-200" 
                 x-transition:enter-start="opacity-0 -translate-y-2" 
                 x-transition:enter-end="opacity-100 translate-y-0"
                 x-transition:leave="transition ease-in duration-150" 
                 x-transition:leave-start="opacity-100 translate-y-0" 
                 x-transition:leave-end="opacity-0 -translate-y-2"
                 class="mt-3">
                <form method="GET" action="{{ route('attendance.index') }}" class="card-bg p-3 rounded-xl border border-gray-700 space-y-2.5 shadow-lg relative z-10">
                    <div class="w-full overflow-hidden">
                        <label class="block text-[11px] text-gray-400 mb-1">From</label>
                        <input type="date" name="start_date" value="{{ request('start_date') }}"
                            class="block w-full min-w-0 box-border bg-gray-800 border border-gray-600 rounded-lg text-sm text-white px-2.5 py-1.5 focus:outline-none focus:border-[#f97316] appearance-none">
                    </div>
                    <div class="w-full overflow-hidden">
                        <label class="block text-[11px] text-gray-400 mb-1">To</label>
                        <input type="date" name="end_date" value="{{ request('end_date') }}"
                            class="block w-full min-w-0 box-border bg-gray-800 border border-gray-600 rounded-lg text-sm text-white px-2.5 py-1.5 focus:outline-none focus:border-[#f97316] appearance-none">
                    </div>
                    <div class="flex items-center space-x-2 pt-1">
                        <button type="submit"
                            class="flex-1 bg-[#f97316] text-white px-3 py-1.5 rounded-lg text-sm font-semibold hover:bg-[#ea580c] transition flex items-center justify-center space-x-1.5">
                            <i class="fa-solid fa-check text-xs"></i>
                            <span>Apply</span>
                        </button>
                        @if(request()->hasAny(['start_date', 'end_date']))
                            <a href="{{ route('attendance.index') }}"
                                class="bg-gray-600 text-white px-3 py-1.5 rounded-lg text-sm font-semibold hover:bg-gray-500 transition flex items-center justify-center">
                                Clear
                            </a>
                        @endif
                    </div>
                </form>
            </div>
        </div>

        <div class="space-y-4">
            @forelse($attendancesHistory as $att)
                <div class="card-bg rounded-xl p-4 border border-gray-700">
                    <div class="flex justify-between items-center mb-3">
                        <div>
                            <p class="font-bold text-gray-200">
                                {{ \Carbon\Carbon::parse($att->date)->translatedFormat('l, d M Y') }}</p>
                            <p class="text-xs text-gray-400 mt-1">Status:
                                <span
                                    class="{{ $att->status === 'PRESENT' ? 'text-green-500' : ($att->status === 'ABSENT' ? 'text-red-500' : 'text-orange-500') }} font-semibold">{{ $att->status }}</span>
                            </p>
                        </div>
                    </div>

                    <div class="space-y-2">
                        <!-- Shift Times -->
                        <div
                            class="flex items-center justify-between bg-gray-800/50 rounded-lg p-3 border border-gray-700/50">
                            <div class="text-center w-1/3">
                                <p class="text-[10px] text-gray-400 uppercase tracking-wider mb-1">Check In</p>
                                @php $inEvent = $att->events->where('event_type', \App\Enums\EventType::START_SHIFT)->first() ?? $att->events->where('event_type', 'START_SHIFT')->first(); @endphp
                                <p class="font-bold {{ $inEvent ? 'text-white' : 'text-gray-500' }}">
                                    {{ $inEvent ? \Carbon\Carbon::parse($inEvent->timestamp)->format('H:i') : '--:--' }}
                                </p>
                                @if($inEvent && $inEvent->latitude && $inEvent->longitude)
                                    <a href="https://maps.google.com/?q={{ $inEvent->latitude }},{{ $inEvent->longitude }}"
                                        target="_blank"
                                        class="text-[10px] text-blue-400 hover:text-blue-300 hover:underline mt-1 block"><i
                                            class="fa-solid fa-location-dot"></i> Maps</a>
                                @endif
                            </div>
                            <div class="text-center text-gray-500">
                                <i class="fa-solid fa-arrow-right text-xs"></i>
                            </div>
                            <div class="text-center w-1/3">
                                <p class="text-[10px] text-gray-400 uppercase tracking-wider mb-1">Check Out</p>
                                @php $outEvent = $att->events->where('event_type', \App\Enums\EventType::END_SHIFT)->first() ?? $att->events->where('event_type', 'END_SHIFT')->first(); @endphp
                                <p class="font-bold {{ $outEvent ? 'text-white' : 'text-gray-500' }}">
                                    {{ $outEvent ? \Carbon\Carbon::parse($outEvent->timestamp)->format('H:i') : '--:--' }}
                                </p>
                                @if($outEvent && $outEvent->latitude && $outEvent->longitude)
                                    <a href="https://maps.google.com/?q={{ $outEvent->latitude }},{{ $outEvent->longitude }}"
                                        target="_blank"
                                        class="text-[10px] text-blue-400 hover:text-blue-300 hover:underline mt-1 block"><i
                                            class="fa-solid fa-location-dot"></i> Maps</a>
                                @endif
                            </div>
                        </div>

                        <!-- Break Times -->
                        <div
                            class="flex items-center justify-between bg-gray-800/50 rounded-lg p-3 border border-gray-700/50">
                            <div class="text-center w-1/3">
                                <p class="text-[10px] text-orange-400 uppercase tracking-wider mb-1">Break Start</p>
                                @php $breakInEvent = $att->events->where('event_type', \App\Enums\EventType::START_BREAK)->first() ?? $att->events->where('event_type', 'START_BREAK')->first(); @endphp
                                <p class="font-bold {{ $breakInEvent ? 'text-white' : 'text-gray-500' }}">
                                    {{ $breakInEvent ? \Carbon\Carbon::parse($breakInEvent->timestamp)->format('H:i') : '--:--' }}
                                </p>
                                @if($breakInEvent && $breakInEvent->latitude && $breakInEvent->longitude)
                                    <a href="https://maps.google.com/?q={{ $breakInEvent->latitude }},{{ $breakInEvent->longitude }}"
                                        target="_blank"
                                        class="text-[10px] text-blue-400 hover:text-blue-300 hover:underline mt-1 block"><i
                                            class="fa-solid fa-location-dot"></i> Maps</a>
                                @endif
                            </div>
                            <div class="text-center text-gray-500">
                                <i class="fa-solid fa-arrow-right text-xs"></i>
                            </div>
                            <div class="text-center w-1/3">
                                <p class="text-[10px] text-blue-400 uppercase tracking-wider mb-1">Break End</p>
                                @php $breakOutEvent = $att->events->where('event_type', \App\Enums\EventType::END_BREAK)->first() ?? $att->events->where('event_type', 'END_BREAK')->first(); @endphp
                                <p class="font-bold {{ $breakOutEvent ? 'text-white' : 'text-gray-500' }}">
                                    {{ $breakOutEvent ? \Carbon\Carbon::parse($breakOutEvent->timestamp)->format('H:i') : '--:--' }}
                                </p>
                                @if($breakOutEvent && $breakOutEvent->latitude && $breakOutEvent->longitude)
                                    <a href="https://maps.google.com/?q={{ $breakOutEvent->latitude }},{{ $breakOutEvent->longitude }}"
                                        target="_blank"
                                        class="text-[10px] text-blue-400 hover:text-blue-300 hover:underline mt-1 block"><i
                                            class="fa-solid fa-location-dot"></i> Maps</a>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            @empty
                <div class="text-center py-10 text-gray-500 border border-dashed border-gray-700 rounded-xl">
                    <i class="fa-solid fa-calendar-xmark text-3xl mb-3 opacity-50"></i>
                    <p class="text-sm font-medium">Belum ada riwayat kehadiran.</p>
                </div>
            @endforelse
        </div>
    </main>

    <!-- Profile Content -->
    <main class="px-5 pt-12 mt-4" x-show="currentNav === 'profile'" style="display: none;"
        x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 scale-95"
        x-transition:enter-end="opacity-100 scale-100">
        <h1 class="text-2xl font-bold mb-6">Profile</h1>

        <div class="card-bg rounded-xl p-6 text-center mb-6">
            <img src="{{ $user->avatar && Storage::disk('public')->exists($user->avatar) ? Storage::url($user->avatar) : asset('userdefault-160x160.jpg') }}"
                class="w-24 h-24 rounded-full border-4 border-gray-600 bg-white mx-auto mb-4 object-cover">
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
                <p class="text-xs text-gray-500 mb-1">Employee ID</p>
                <p class="font-medium text-gray-200">{{ $user->employee_id ?? 'Not set' }}</p>
            </div>
            <div class="h-[1px] bg-gray-700"></div>
            <div>
                <p class="text-xs text-gray-500 mb-1">NIK (KTP)</p>
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
        <div @click="currentNav = 'history'"
            :class="currentNav === 'history' ? 'text-orange-500' : 'text-gray-400 hover:text-gray-200'"
            class="flex flex-col items-center cursor-pointer w-16 transition">
            <i class="fa-solid fa-clock-rotate-left text-xl mb-1"></i>
            <span class="text-[10px] font-semibold">History</span>
        </div>
        <div @click="currentNav = 'profile'"
            :class="currentNav === 'profile' ? 'text-orange-500' : 'text-gray-400 hover:text-gray-200'"
            class="flex flex-col items-center cursor-pointer transition w-16">
            <div class="w-6 h-6 rounded-full overflow-hidden border mb-1 transition-colors"
                :class="currentNav === 'profile' ? 'border-orange-500' : 'border-gray-500'">
                <img src="{{ $user->avatar && Storage::disk('public')->exists($user->avatar) ? Storage::url($user->avatar) : asset('userdefault-160x160.jpg') }}"
                    class="w-full h-full object-cover">
            </div>
            <span class="text-[10px] font-medium">Profile</span>
        </div>
    </nav>

    <!-- Camera Modal -->
    <div x-show="showCamera" style="display: none;" class="fixed inset-0 bg-black z-[110] flex flex-col justify-between">
        <!-- Header -->
        <div class="flex items-center justify-between px-4 py-5 bg-black w-full z-10">
            <h2 class="text-white font-semibold text-lg">Ambil Foto</h2>
            <button @click="closeCameraUI()" class="text-white p-2">
                <i class="fa-solid fa-xmark text-xl"></i>
            </button>
        </div>
        
        <!-- Video Stream -->
        <div class="relative w-full aspect-[3/4] bg-black flex-shrink-0">
            <video id="cameraStream" autoplay playsinline class="absolute inset-0 w-full h-full object-cover transform -scale-x-100"></video>
            <!-- Headroom Overlay Guide -->
            <img src="{{ asset('Headroom.png') }}" class="absolute inset-0 w-full h-full object-contain pointer-events-none z-10 opacity-70">
            <canvas id="cameraCanvas" class="hidden"></canvas>
        </div>

        <!-- Capture Button -->
        <div class="flex-1 flex justify-center items-center bg-black w-full pb-8">
            <button @click="capturePhoto()" class="w-20 h-20 bg-white/20 rounded-full border-[6px] border-white flex items-center justify-center active:scale-90 transition">
                <div class="w-16 h-16 bg-white rounded-full"></div>
            </button>
        </div>
    </div>

    <!-- Preview Modal -->
    <div x-show="showPreview" style="display: none;" class="fixed inset-0 bg-[#272933] z-[100] flex flex-col overflow-y-auto">
        <!-- Header -->
        <div class="flex items-center px-4 py-4 bg-[#313340]">
            <button @click="showPreview = false; previewImage = null" class="text-white mr-4">
                <i class="fa-solid fa-arrow-left"></i>
            </button>
            <h2 class="text-white font-semibold text-lg">Preview</h2>
        </div>
        
        <!-- Map -->
        <div class="relative w-full h-72 bg-gray-800">
            <div id="previewMap" class="w-full h-full z-0 relative"></div>
            <div class="absolute bottom-2 left-1/2 transform -translate-x-1/2 bg-gray-900/80 text-white text-xs px-3 py-1.5 rounded-md z-[40]" x-text="'Location accuracy ' + (locationData.accuracy ? Math.round(locationData.accuracy) + ' meters' : '...')">
            </div>
        </div>

        <div class="flex-1 px-5 py-4 flex flex-col items-center text-center">
            <h3 class="text-lg font-semibold text-white">{{ $user->name }}</h3>
            <p class="text-sm text-gray-400 mb-4">{{ now()->translatedFormat('M, d Y, H:i') }}</p>
            
            <img :src="previewImage" class="w-64 h-80 object-cover rounded-xl mb-2 border border-gray-600">
            <p class="text-xs text-gray-400 mb-6">Attendance Photo</p>

            
            <div class="flex items-center justify-center space-x-3 mb-3">
                <p class="text-sm font-medium" :class="locationValid ? 'text-green-500' : 'text-red-500'" x-text="locationStatusText"></p>
                <button type="button" @click="getLocationAndInitMap()" class="text-gray-400 hover:text-white transition bg-gray-800 w-8 h-8 flex items-center justify-center rounded-full border border-gray-600 active:scale-95">
                    <i class="fa-solid fa-arrows-rotate"></i>
                </button>
            </div>
            
            <button @click="submitAttendance()" :disabled="!locationValid" class="w-full bg-[#f97316] hover:bg-[#ea580c] text-white font-bold py-3.5 rounded-xl transition active:scale-95 shadow-md disabled:opacity-50 disabled:cursor-not-allowed flex justify-center items-center">
                <span x-show="!isSubmitting">Save Attendance</span>
                <span x-show="isSubmitting" style="display: none;"><i class="fa-solid fa-spinner fa-spin mr-2"></i> Saving...</span>
            </button>
        </div>
    </div>

    <!-- Trigger preview when file is selected -->
    <script>
        let videoStream = null;
        let currentTargetFormId = null;

        function openCameraUI(formId) {
            currentTargetFormId = formId;
            const alpineData = window.alpineApp;
            alpineData.showCamera = true;
            
            navigator.mediaDevices.getUserMedia({ video: { facingMode: "user", aspectRatio: { ideal: 0.75 } } })
                .then(function(stream) {
                    videoStream = stream;
                    const video = document.getElementById('cameraStream');
                    video.srcObject = stream;
                    video.play();
                })
                .catch(function(err) {
                    alert("Unable to access camera: " + err.message);
                    alpineData.showCamera = false;
                });
        }

        function closeCameraUI() {
            if (videoStream) {
                videoStream.getTracks().forEach(track => track.stop());
                videoStream = null;
            }
            window.alpineApp.showCamera = false;
        }

        function capturePhoto() {
            const video = document.getElementById('cameraStream');
            const canvas = document.getElementById('cameraCanvas');
            const context = canvas.getContext('2d');
            
            const targetRatio = 3 / 4;
            const videoRatio = video.videoWidth / video.videoHeight;
            
            let sourceWidth = video.videoWidth;
            let sourceHeight = video.videoHeight;
            let offsetX = 0;
            let offsetY = 0;
            
            if (videoRatio > targetRatio) {
                // Video is wider than 3:4. Crop sides.
                sourceWidth = video.videoHeight * targetRatio;
                offsetX = (video.videoWidth - sourceWidth) / 2;
            } else {
                // Video is taller than 3:4. Crop top/bottom.
                sourceHeight = video.videoWidth / targetRatio;
                offsetY = (video.videoHeight - sourceHeight) / 2;
            }
            
            canvas.width = sourceWidth;
            canvas.height = sourceHeight;
            
            // Mirror the canvas context since we mirrored the video visually
            context.translate(canvas.width, 0);
            context.scale(-1, 1);
            
            // Draw video frame to canvas with crop to match the 3:4 UI container
            context.drawImage(video, offsetX, offsetY, sourceWidth, sourceHeight, 0, 0, canvas.width, canvas.height);
            
            canvas.toBlob(function(blob) {
                const file = new File([blob], "selfie.jpg", { type: "image/jpeg" });
                const dataTransfer = new DataTransfer();
                dataTransfer.items.add(file);
                
                let fileInput = null;
                if (currentTargetFormId === 'attendanceForm') {
                    fileInput = document.getElementById('selfie_input');
                } else {
                    fileInput = document.getElementById('break_selfie_input');
                }
                fileInput.files = dataTransfer.files;
                
                // Dispatch preview-ready event
                const url = URL.createObjectURL(file);
                window.dispatchEvent(new CustomEvent('preview-ready', {
                    detail: { image: url, formId: currentTargetFormId }
                }));
                
                closeCameraUI();
            }, 'image/jpeg', 0.8);
        }

        function submitAttendance() {
            const alpineData = window.alpineApp;
            alpineData.isSubmitting = true;
            const form = document.getElementById(alpineData.currentFormId);
            form.submit();
        }

        // Geofencing data
        const officeLat = {{ $user->location->latitude ?? 'null' }};
        const officeLng = {{ $user->location->longitude ?? 'null' }};
        const officeRadius = {{ $user->location->radius ?? 'null' }};

        function calculateDistance(lat1, lon1, lat2, lon2) {
            if (lat1 === null || lon1 === null || lat2 === null || lon2 === null) return null;
            const R = 6371e3; // metres
            const φ1 = lat1 * Math.PI/180; // φ, λ in radians
            const φ2 = lat2 * Math.PI/180;
            const Δφ = (lat2-lat1) * Math.PI/180;
            const Δλ = (lon2-lon1) * Math.PI/180;

            const a = Math.sin(Δφ/2) * Math.sin(Δφ/2) +
                      Math.cos(φ1) * Math.cos(φ2) *
                      Math.sin(Δλ/2) * Math.sin(Δλ/2);
            const c = 2 * Math.atan2(Math.sqrt(a), Math.sqrt(1-a));

            return R * c; // in metres
        }

        let leafletMap = null;
        let mapMarker = null;
        let mapCircle = null;

        function getLocationAndInitMap() {
            const alpineData = window.alpineApp;
            alpineData.locationStatusText = 'Getting GPS Location...';
            alpineData.locationValid = false;

            if ("geolocation" in navigator) {
                navigator.geolocation.getCurrentPosition(function (position) {
                    const currentLat = position.coords.latitude;
                    const currentLng = position.coords.longitude;
                    const accuracy = position.coords.accuracy;

                    alpineData.locationData = {
                        lat: currentLat,
                        lng: currentLng,
                        accuracy: accuracy,
                        distance: null
                    };

                    document.getElementById('lat').value = currentLat;
                    document.getElementById('lng').value = currentLng;
                    document.getElementById('lat2').value = currentLat;
                    document.getElementById('lng2').value = currentLng;

                    setTimeout(() => {
                        if (!leafletMap) {
                            leafletMap = L.map('previewMap').setView([currentLat, currentLng], 15);
                            L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                                attribution: '&copy; OpenStreetMap'
                            }).addTo(leafletMap);
                            
                            mapMarker = L.marker([currentLat, currentLng]).addTo(leafletMap).bindPopup("Lokasi Anda").openPopup();
                        } else {
                            leafletMap.setView([currentLat, currentLng], 15);
                            mapMarker.setLatLng([currentLat, currentLng]);
                            leafletMap.invalidateSize();
                        }

                        if (officeLat !== null && officeLng !== null) {
                            const distance = calculateDistance(currentLat, currentLng, officeLat, officeLng);
                            alpineData.locationData.distance = distance;

                            if (mapCircle) {
                                mapCircle.remove();
                            }

                            const isValid = distance <= officeRadius;
                            alpineData.locationValid = isValid;

                            if (isValid) {
                                alpineData.locationStatusText = 'Dalam Radius (' + Math.round(distance) + 'm)';
                            } else {
                                alpineData.locationStatusText = 'Di Luar Radius (' + Math.round(distance) + 'm / Max ' + officeRadius + 'm)';
                            }

                            mapCircle = L.circle([officeLat, officeLng], {
                                color: isValid ? 'green' : 'red',
                                fillColor: isValid ? '#3f0' : '#f03',
                                fillOpacity: 0.2,
                                radius: officeRadius
                            }).addTo(leafletMap);
                            
                            L.marker([officeLat, officeLng]).addTo(leafletMap).bindPopup("Lokasi Kantor");
                            
                            var bounds = L.latLngBounds([[currentLat, currentLng], [officeLat, officeLng]]);
                            leafletMap.fitBounds(bounds, {padding: [20, 20]});
                        } else {
                            alpineData.locationStatusText = 'Anda tidak memiliki lokasi kerja';
                        }
                    }, 100);

                }, function (error) {
                    alpineData.locationStatusText = 'Error Getting GPS Location';
                }, {
                    enableHighAccuracy: true,
                    timeout: 10000,
                    maximumAge: 0
                });
            } else {
                alpineData.locationStatusText = 'Browser does not support GPS';
            }
        }
    </script>
</body>

</html>