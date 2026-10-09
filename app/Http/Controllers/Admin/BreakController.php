<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Attendance;
use Maatwebsite\Excel\Facades\Excel;

class BreakController extends Controller
{
    public function index(Request $request)
    {
        // Get attendances that have any break events
        $query = Attendance::with(['user.location', 'shift', 'events'])
            ->whereHas('events', function($q) {
                $q->whereIn('event_type', ['START_BREAK', 'END_BREAK']);
            })
            ->orderBy('date', 'desc');

        if ($request->filled('search')) {
            $search = $request->search;
            $query->whereHas('user', function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('nik', 'like', "%{$search}%");
            });
        }

        if ($request->filled('date')) {
            $query->whereDate('date', $request->date);
        }

        $perPage = $request->input('per_page', 10);
        $attendances = $query->paginate($perPage)->appends($request->all());

        return view('admin.breaks.index', compact('attendances'));
    }
}
