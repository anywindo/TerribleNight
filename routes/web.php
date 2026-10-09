<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\AttendanceController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\RoleController;
use App\Http\Controllers\Admin\EmployeeController;
use App\Http\Controllers\Admin\ShiftController;
use App\Http\Controllers\Admin\LocationController;
use App\Http\Controllers\Admin\AttendanceController as AdminAttendanceController;
use App\Http\Controllers\Admin\BreakController;

Route::get('/', function () {
    return view('auth.login');
})->name('login');

Route::post('/login', [AuthController::class, 'login']);
Route::any('/logout', [AuthController::class, 'logout'])->name('logout');

Route::middleware('auth')->group(function () {
    // Role Selection
    Route::get('/role-selection', function () {
        if (!auth()->user()->isAdmin()) {
            return redirect('/attendance');
        }
        
        if (auth()->user()->isSuperAdmin()) {
            return redirect()->route('admin.dashboard');
        }

        return view('auth.role-selection');
    })->name('role.selection');

    // Employee Routes
    Route::get('/attendance', [AttendanceController::class, 'index'])->name('attendance.index');
    Route::post('/attendance', [AttendanceController::class, 'store'])->name('attendance.store');
    
    // HR / Admin Routes
    Route::middleware(['role:admin|HR|super-admin'])->prefix('admin')->name('admin.')->group(function () {
        Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
        
        Route::middleware(['permission:manage-rbac'])->group(function () {
            Route::resource('roles', RoleController::class);
        });
        Route::get('employees/template', [EmployeeController::class, 'downloadTemplate'])->name('employees.template');
        Route::post('employees/import', [EmployeeController::class, 'import'])->name('employees.import');
        Route::get('employees/export', [EmployeeController::class, 'export'])->name('employees.export');
        Route::resource('employees', EmployeeController::class);
        Route::resource('shifts', ShiftController::class);
        Route::resource('locations', LocationController::class);

        Route::get('attendances/export', [AdminAttendanceController::class, 'export'])->name('attendances.export');
        Route::get('attendances', [AdminAttendanceController::class, 'index'])->name('attendances.index');
        
        Route::get('breaks', [BreakController::class, 'index'])->name('breaks.index');

        // Settings
        Route::get('settings', [\App\Http\Controllers\Admin\SettingController::class, 'index'])->name('settings.index');
        Route::post('settings', [\App\Http\Controllers\Admin\SettingController::class, 'update'])->name('settings.update');
    });
});
