<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Attendance;
use App\Models\AttendanceEvent;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;

class AttendanceController extends Controller
{
    public function index(Request $request)
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
        
        $historyQuery = \App\Models\Attendance::where('user_id', $user->id);
        
        if ($request->filled('start_date')) {
            $historyQuery->whereDate('date', '>=', $request->start_date);
        }
        if ($request->filled('end_date')) {
            $historyQuery->whereDate('date', '<=', $request->end_date);
        }

        $attendancesHistory = $historyQuery->orderBy('date', 'desc')
            ->take(30)
            ->get();
            
        $shift = $attendance ? $attendance->shift : \App\Models\Shift::first();
        
        return view('attendance.index', compact('user', 'events', 'historyEvents', 'attendancesHistory', 'nextAction', 'activeBreak', 'shift'));
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
        
        // Check for lateness if START_SHIFT
        $status = 'EXACT';
        $latenessMinutes = 0;
        
        if ($request->event_type === 'START_SHIFT') {
            $shift = \App\Models\Shift::find(1); // Default shift
            if ($shift && $shift->default_start_time) {
                $scheduledStart = \Carbon\Carbon::parse($today->format('Y-m-d') . ' ' . $shift->default_start_time);
                $ruleEnabled = \App\Models\Setting::get('attendance_rule_enabled', false);
                $cutoffMinutes = $ruleEnabled ? \App\Models\Setting::get('attendance_rule_minutes', 15) : 0;
                
                $lateThreshold = $scheduledStart->copy()->addMinutes($cutoffMinutes);
                $now = now();
                
                if ($now->greaterThan($lateThreshold)) {
                    $latenessMinutes = (int) abs($now->diffInMinutes($scheduledStart));
                    $status = 'LATE';
                } elseif ($now->lessThan($scheduledStart)) {
                    $status = 'EARLY';
                }
            }
        }

        // Find or create today's attendance record
        $attendance = Attendance::firstOrCreate(
            ['user_id' => $user->id, 'date' => $today],
            ['status' => $status, 'shift_id' => 1] // Using dynamic valid status
        );
        
        // If it's START_SHIFT and attendance was just created or we need to update status
        if ($request->event_type === 'START_SHIFT') {
            $attendance->update(['status' => $status]);
        }
        
        $selfiePath = $fileUploadService->uploadImage($request->file('selfie'), 'attendance-selfies');
        
        AttendanceEvent::create([
            'attendance_id' => $attendance->id,
            'event_type' => $request->event_type,
            'timestamp' => now(),
            'latitude' => $request->latitude,
            'longitude' => $request->longitude,
            'selfie_path' => $selfiePath,
        ]);
        
        $message = 'Kehadiran berhasil dicatat!';

        // Notification for late check-in
        if ($request->event_type === 'START_SHIFT' && $status === 'LATE') {
            $admins = \App\Models\User::role(['super-admin', 'HR'])->get();
            \Illuminate\Support\Facades\Notification::send($admins, new \App\Notifications\EmployeeLateNotification($user, $latenessMinutes));
            
            $message .= ' Anda terlambat ' . $latenessMinutes . ' menit.';
        }
        
        return redirect()->route('attendance.index')->with('success', $message);
    }
}
