<?php

namespace App\Http\Controllers\Api\V1\Employee;

use App\Enums\EventType;
use App\Http\Controllers\Controller;
use App\Http\Requests\StoreAttendanceEventRequest;
use App\Http\Resources\AttendanceResource;
use App\Models\Shift;
use App\Repositories\Contracts\AttendanceRepositoryInterface;
use App\Services\AttendanceService;
use App\Services\FileUploadService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class AttendanceController extends Controller
{
    public function __construct(
        protected AttendanceService $attendanceService,
        protected AttendanceRepositoryInterface $attendanceRepository,
        protected FileUploadService $fileUploadService
    ) {
    }

    /**
     * View own attendance records.
     */
    public function history(Request $request)
    {
        // Simple pagination for now. 
        // Real implementation would use the repository's paginate method with filters
        $attendances = $this->attendanceRepository->paginate(15, ['*'], [
            'user_id' => $request->user()->id
        ]);
        
        // In this boilerplate setup, we might need a specific filter or custom query in the repo
        // For now, let's just use Eloquent directly since it's a specific use-case
        $attendances = $request->user()->attendances()->with('events')->latest('date')->paginate(15);

        return AttendanceResource::collection($attendances);
    }

    /**
     * Submit an attendance event (e.g. check-in, check-out)
     */
    public function storeEvent(StoreAttendanceEventRequest $request): JsonResponse
    {
        $employee = $request->user();
        $shift = Shift::findOrFail($request->validated('shift_id'));
        $eventType = EventType::from($request->validated('event_type'));
        
        $selfiePath = null;
        if ($request->hasFile('selfie')) {
            $selfiePath = $this->fileUploadService->uploadImage($request->file('selfie'));
        }

        $payload = $request->validated();
        $payload['selfie_path'] = $selfiePath;

        $attendance = $this->attendanceService->recordEvent($employee, $shift, $eventType, $payload);

        return response()->json([
            'message' => 'Attendance event recorded successfully.',
            'data' => new AttendanceResource($attendance)
        ], 201);
    }
}
