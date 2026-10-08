<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Attendance;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\Admin\AttendanceExport;

class AttendanceController extends Controller
{
    public function index(Request $request)
    {
        $query = Attendance::with(['user', 'shift', 'events'])->orderBy('date', 'desc');

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

        $perPage = $request->get('per_page', 10);
        $attendances = $query->paginate($perPage)->appends($request->all());

        return view('admin.attendances.index', compact('attendances'));
    }

    public function export(Request $request)
    {
        $startDate = null;
        $endDate = null;

        if ($request->export_type === 'today') {
            $startDate = now()->toDateString();
            $endDate = now()->toDateString();
        } elseif ($request->export_type === 'custom') {
            $startDate = $request->start_date;
            $endDate = $request->end_date;
        } // 'all' will leave them as null

        return Excel::download(new AttendanceExport($startDate, $endDate), 'attendance_'.date('Y-m-d_H-i-s').'.xlsx');
    }
}
