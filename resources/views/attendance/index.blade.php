<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=0">
    <title>Presensi | SIMSDM Garment</title>
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

<body class="min-h-screen pb-24"
    x-data="{ activeTab: 'work', currentNav: '{{ request()->hasAny(['start_date', 'end_date']) ? 'history' : 'home' }}' }">
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
                        <button type="button" onclick="document.getElementById('selfie_input').click()"
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
                            <button type="button" onclick="document.getElementById('selfie_input').click()"
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
                                <button type="button" onclick="document.getElementById('break_selfie_input').click()"
                                    class="flex-1 bg-[#f97316] hover:bg-[#ea580c] text-white font-bold py-3 rounded-lg transition active:scale-95 shadow-md">
                                    Record Break Time
                                </button>
                            @endif
                        @elseif(!$endBreak)
                            <input type="hidden" name="event_type" value="END_BREAK">
                            <button type="button" onclick="document.getElementById('break_selfie_input').click()"
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

        <form method="GET" action="{{ route('attendance.index') }}"
            class="mb-6 card-bg p-3 rounded-xl border border-gray-700">
            <div class="flex items-end space-x-2">
                <div class="flex-1">
                    <label class="block text-xs text-gray-400 mb-1">From</label>
                    <input type="date" name="start_date" value="{{ request('start_date') }}"
                        class="w-full bg-gray-800 border border-gray-600 rounded-lg text-sm text-white px-2 py-1.5 focus:outline-none focus:border-[#f97316]">
                </div>
                <div class="flex-1">
                    <label class="block text-xs text-gray-400 mb-1">To</label>
                    <input type="date" name="end_date" value="{{ request('end_date') }}"
                        class="w-full bg-gray-800 border border-gray-600 rounded-lg text-sm text-white px-2 py-1.5 focus:outline-none focus:border-[#f97316]">
                </div>
                <button type="submit"
                    class="bg-[#f97316] text-white px-3 py-1.5 rounded-lg text-sm font-semibold hover:bg-[#ea580c] transition h-[34px]">
                    <i class="fa-solid fa-filter"></i>
                </button>
                @if(request()->hasAny(['start_date', 'end_date']))
                    <a href="{{ route('attendance.index') }}"
                        class="bg-gray-600 text-white px-3 py-1.5 rounded-lg text-sm font-semibold hover:bg-gray-500 transition h-[34px] flex items-center justify-center">
                        <i class="fa-solid fa-xmark"></i>
                    </a>
                @endif
            </div>
        </form>

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

        function disableAttendanceButtons() {
            const buttons = document.querySelectorAll('button[onclick*="selfie_input"]');
            buttons.forEach(btn => {
                btn.disabled = true;
                btn.classList.add('opacity-50', 'cursor-not-allowed');
                btn.onclick = null;
            });
            document.getElementById('selfie_input').disabled = true;
            document.getElementById('break_selfie_input').disabled = true;
        }

        // Geolocation
        document.addEventListener("DOMContentLoaded", function () {
            if ("geolocation" in navigator) {
                navigator.geolocation.getCurrentPosition(function (position) {
                    const currentLat = position.coords.latitude;
                    const currentLng = position.coords.longitude;

                    document.getElementById('lat').value = currentLat;
                    document.getElementById('lng').value = currentLng;
                    document.getElementById('lat2').value = currentLat;
                    document.getElementById('lng2').value = currentLng;

                    if (officeLat === null || officeLng === null || officeRadius === null) {
                        document.getElementById('locationStatus').innerHTML = '<i class="fa-solid fa-triangle-exclamation text-red-500 mr-1"></i> Anda tidak memiliki lokasi kerja';
                        document.getElementById('locationStatus').classList.add('text-red-400');
                        disableAttendanceButtons();
                        return;
                    }

                    const distance = calculateDistance(currentLat, currentLng, officeLat, officeLng);

                    if (distance > officeRadius) {
                        document.getElementById('locationStatus').innerHTML = '<i class="fa-solid fa-triangle-exclamation text-red-500 mr-1"></i> Di Luar Radius (' + Math.round(distance) + 'm / Max ' + officeRadius + 'm)';
                        document.getElementById('locationStatus').classList.add('text-red-400');
                        disableAttendanceButtons();
                    } else {
                        document.getElementById('locationStatus').innerHTML = '<i class="fa-solid fa-location-dot text-green-500 mr-1"></i> Dalam Radius (' + Math.round(distance) + 'm)';
                        document.getElementById('locationStatus').classList.add('text-green-400');
                    }
                }, function (error) {
                    document.getElementById('locationStatus').innerHTML = '<i class="fa-solid fa-triangle-exclamation text-red-500 mr-1"></i> Error Getting GPS Location';
                    document.getElementById('locationStatus').classList.add('text-red-400');
                    disableAttendanceButtons();
                }, {
                    enableHighAccuracy: true,
                    timeout: 10000,
                    maximumAge: 0
                });
            } else {
                document.getElementById('locationStatus').innerHTML = 'Browser does not support GPS';
                disableAttendanceButtons();
            }
        });
    </script>
</body>

</html>