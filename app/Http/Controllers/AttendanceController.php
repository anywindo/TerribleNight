<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Attendance;
use App\Models\AttendanceEvent;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;

class AttendanceController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        $today = Carbon::today();
        
        // Find today's attendance record
        $attendance = Attendance::where('user_id', $user->id)
            ->whereDate('date', $today)
            ->first();
            
        $events = $attendance ? $attendance->events()->orderBy('timestamp', 'asc')->get() : collect();
        
        // Determine the next allowed action based on existing events
        $nextAction = 'START_SHIFT';
        $activeBreak = false;
        
        if ($events->isNotEmpty()) {
            $lastEvent = $events->last()->event_type;
            
            if ($lastEvent === 'START_SHIFT') {
                $nextAction = 'START_BREAK_OR_END_SHIFT';
            } elseif ($lastEvent === 'START_BREAK') {
                $nextAction = 'END_BREAK';
                $activeBreak = true;
            } elseif ($lastEvent === 'END_BREAK') {
                $nextAction = 'START_BREAK_OR_END_SHIFT';
            } elseif ($lastEvent === 'END_SHIFT') {
                $nextAction = 'COMPLETED';
            }
        }
        $historyEvents = AttendanceEvent::whereHas('attendance', function ($query) use ($user) {
            $query->where('user_id', $user->id);
        })->orderBy('timestamp', 'desc')->take(20)->get();
        
        return view('attendance.index', compact('user', 'events', 'historyEvents', 'nextAction', 'activeBreak'));
    }

    public function store(Request $request, \App\Services\FileUploadService $fileUploadService)
    {
        $request->validate([
            'event_type' => 'required|in:START_SHIFT,START_BREAK,END_BREAK,END_SHIFT',
            'latitude' => 'required|numeric',
            'longitude' => 'required|numeric',
            'selfie' => 'required|image|max:10240'
        ]);
        
        $user = Auth::user();
        $today = Carbon::today();
        
        // Find or create today's attendance record
        $attendance = Attendance::firstOrCreate(
            ['user_id' => $user->id, 'date' => $today],
            ['status' => 'PRESENT', 'shift_id' => 1] // Assuming default status and shift
        );
        
        $selfiePath = $fileUploadService->uploadImage($request->file('selfie'), 'attendance-selfies');
        
        AttendanceEvent::create([
            'attendance_id' => $attendance->id,
            'event_type' => $request->event_type,
            'timestamp' => now(),
            'latitude' => $request->latitude,
            'longitude' => $request->longitude,
            'selfie_path' => $selfiePath,
        ]);
        
        return redirect()->route('attendance.index')->with('success', 'Kehadiran berhasil dicatat!');
    }
}
