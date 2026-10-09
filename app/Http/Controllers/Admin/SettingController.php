<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class SettingController extends Controller
{
    public function index()
    {
        $attendanceRuleEnabled = \App\Models\Setting::get('attendance_rule_enabled', false);
        $attendanceRuleMinutes = \App\Models\Setting::get('attendance_rule_minutes', 15);

        return view('admin.settings.index', compact('attendanceRuleEnabled', 'attendanceRuleMinutes'));
    }

    public function update(Request $request)
    {
        $request->validate([
            'attendance_rule_enabled' => 'nullable|boolean',
            'attendance_rule_minutes' => 'required|integer|min:0',
        ]);

        \App\Models\Setting::set('attendance_rule_enabled', $request->has('attendance_rule_enabled') ? 'true' : 'false', 'boolean');
        \App\Models\Setting::set('attendance_rule_minutes', $request->input('attendance_rule_minutes'), 'integer');

        return back()->with('success', 'Pengaturan berhasil diperbarui.');
    }
}
