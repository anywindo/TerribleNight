<?php

namespace App\Http\Controllers\Api\V1\Admin;

use App\Http\Controllers\Controller;
use App\Http\Resources\AttendanceResource;
use App\Repositories\Contracts\AttendanceRepositoryInterface;
use Illuminate\Http\Request;

class AttendanceController extends Controller
{
    public function __construct(
        protected AttendanceRepositoryInterface $attendanceRepository
    ) {
    }

    /**
     * View all attendances (for HR/Admin).
     */
    public function index(Request $request)
    {
        // Here we could filter by date, employee, shift, etc.
        $attendances = $this->attendanceRepository->paginate($request->input('per_page', 15));
        
        return AttendanceResource::collection($attendances);
    }
}
