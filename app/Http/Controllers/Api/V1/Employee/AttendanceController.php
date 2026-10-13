<?php

namespace App\Http\Controllers\Api\V1\Employee;

use App\Enums\EventType;
use App\Http\Controllers\Controller;
use App\Http\Requests\StoreAttendanceEventRequest;
use App\Http\Resources\AttendanceResource;
use App\Models\Attendance;
use App\Models\Shift;
use App\Repositories\Contracts\AttendanceRepositoryInterface;
use App\Services\AttendanceService;
use App\Services\FileUploadService;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class AttendanceController extends Controller
{
    public function __construct(
        protected AttendanceService $attendanceService,
        protected AttendanceRepositoryInterface $attendanceRepository,
        protected FileUploadService $fileUploadService
    ) {
    }

    /**
     * Get today's attendance status and next allowed action for the employee.
     */
    public function today(Request $request): JsonResponse
    {
        $user = $request->user();
        $today = Carbon::today();

        $attendance = Attendance::where('user_id', $user->id)
            ->whereDate('date', $today)
            ->with(['events' => fn($q) => $q->orderBy('timestamp', 'asc'), 'shift'])
            ->first();

        $events = $attendance ? $attendance->events : collect();

        $hasStartedShift = $events->contains(fn($e) => ($e->event_type?->value ?? $e->event_type) === 'START_SHIFT');
        $hasStartedBreak = $events->contains(fn($e) => ($e->event_type?->value ?? $e->event_type) === 'START_BREAK');
        $hasEndedBreak = $events->contains(fn($e) => ($e->event_type?->value ?? $e->event_type) === 'END_BREAK');
        $hasEndedShift = $events->contains(fn($e) => ($e->event_type?->value ?? $e->event_type) === 'END_SHIFT');

        $nextAction = 'START_SHIFT';
        $activeBreak = false;

        if ($hasEndedShift) {
            $nextAction = null;
        } elseif ($hasStartedBreak && !$hasEndedBreak) {
            $nextAction = 'END_BREAK';
            $activeBreak = true;
        } elseif ($hasStartedShift) {
            if ($hasEndedBreak) {
                $nextAction = 'END_SHIFT';
            } else {
                $nextAction = 'START_BREAK';
            }
        }

        $shift = $attendance?->shift ?? Shift::first();
        $workLocation = $user->location;

        $formattedEvents = $events->map(function ($event) {
            return [
                'id' => $event->id,
                'event_type' => $event->event_type instanceof EventType ? $event->event_type->value : (string) $event->event_type,
                'timestamp' => $event->timestamp ? Carbon::parse($event->timestamp)->format('Y-m-d H:i:s') : '',
                'latitude' => $event->latitude !== null ? (float) $event->latitude : null,
                'longitude' => $event->longitude !== null ? (float) $event->longitude : null,
                'selfie_url' => $event->selfie_path ? Storage::url($event->selfie_path) : null,
            ];
        });

        // Build available shifts list for the shift picker
        $availableShifts = Shift::all()->map(fn($s) => [
            'id' => $s->id,
            'name' => $s->shift_name ?? $s->name,
            'start_time' => $s->default_start_time ?? ($s->start_time ?? '08:00:00'),
            'end_time' => $s->default_end_time ?? ($s->end_time ?? '17:00:00'),
        ]);

        return response()->json([
            'meta' => [
                'success' => true,
                'message' => 'Status presensi hari ini berhasil dimuat.'
            ],
            'data' => [
                'date' => $today->toDateString(),
                'status' => $attendance?->status?->value ?? ($attendance?->status ?? 'EXACT'),
                'next_allowed_action' => $nextAction,
                'active_break' => $activeBreak,
                'shift' => $shift ? [
                    'id' => $shift->id,
                    'name' => $shift->shift_name ?? $shift->name,
                    'start_time' => $shift->default_start_time ?? ($shift->start_time ?? '08:00:00'),
                    'end_time' => $shift->default_end_time ?? ($shift->end_time ?? '17:00:00'),
                ] : null,
                'available_shifts' => $availableShifts,
                'work_location' => $workLocation ? [
                    'id' => $workLocation->id,
                    'name' => $workLocation->name,
                    'latitude' => (float) $workLocation->latitude,
                    'longitude' => (float) $workLocation->longitude,
                    'radius' => (float) $workLocation->radius,
                ] : null,
                'events' => $formattedEvents,
            ]
        ]);
    }

    /**
     * View own attendance records.
     */
    public function history(Request $request)
    {
        $attendances = $request->user()->attendances()->with('events')->latest('date')->paginate(15);

        return AttendanceResource::collection($attendances);
    }

    /**
     * Submit an attendance event (e.g. check-in, check-out)
     */
    public function storeEvent(StoreAttendanceEventRequest $request): JsonResponse
    {
        $employee = $request->user();
        $existingAttendance = $this->attendanceRepository->findTodayAttendanceForUser($employee->id);

        $shiftId = $request->validated('shift_id');
        if ($shiftId) {
            $shift = Shift::findOrFail($shiftId);
        } elseif ($existingAttendance && $existingAttendance->shift) {
            $shift = $existingAttendance->shift;
        } else {
            $shift = Shift::first();
        }
        $eventType = EventType::from($request->validated('event_type'));

        // Geofence validation if employee has assigned location
        if ($employee->location) {
            $location = $employee->location;
            $earthRadius = 6371000;
            $latFrom = deg2rad((float) $request->validated('latitude'));
            $lonFrom = deg2rad((float) $request->validated('longitude'));
            $latTo = deg2rad((float) $location->latitude);
            $lonTo = deg2rad((float) $location->longitude);

            $latDelta = $latTo - $latFrom;
            $lonDelta = $lonTo - $lonFrom;

            $angle = 2 * asin(sqrt(pow(sin($latDelta / 2), 2) +
                cos($latFrom) * cos($latTo) * pow(sin($lonDelta / 2), 2)));
                
            $distance = $angle * $earthRadius;
            if ($distance > $location->radius) {
                return response()->json([
                    'meta' => [
                        'success' => false,
                        'message' => 'Jarak Anda ' . round($distance) . ' meter. Melebihi radius maksimal ' . round($location->radius) . ' meter dari lokasi kerja.'
                    ]
                ], 422);
            }
        }
        
        $selfiePath = null;
        if ($request->hasFile('selfie')) {
            $selfiePath = $this->fileUploadService->uploadImage($request->file('selfie'));
        }

        $payload = $request->validated();
        $payload['selfie_path'] = $selfiePath;

        $attendance = $this->attendanceService->recordEvent($employee, $shift, $eventType, $payload);

        return response()->json([
            'meta' => [
                'success' => true,
                'message' => 'Kehadiran berhasil dicatat!'
            ],
            'data' => [
                'event_type' => $eventType->value,
                'timestamp' => now()->format('Y-m-d H:i:s'),
                'status' => $attendance->status?->value ?? ($attendance->status ?? 'EXACT')
            ]
        ], 201);
    }
}
