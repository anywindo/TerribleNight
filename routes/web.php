<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\OnboardingController;
use App\Http\Controllers\AttendanceController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\RoleController;
use App\Http\Controllers\Admin\EmployeeController;
use App\Http\Controllers\Admin\ShiftController;
use App\Http\Controllers\Admin\LocationController;
use App\Http\Controllers\Admin\AttendanceController as AdminAttendanceController;
use App\Http\Controllers\Admin\BreakController;

Route::get('/', function () {
    if (auth()->check()) {
        if (auth()->user()->mustChangePassword()) {
            return redirect()->route('onboarding.password');
        }
        return redirect()->intended(auth()->user()->isAdmin() ? '/role-selection' : '/attendance');
    }
    return view('auth.login');
});

Route::get('/login', function () {
    if (auth()->check()) {
        if (auth()->user()->mustChangePassword()) {
            return redirect()->route('onboarding.password');
        }
        return redirect()->intended(auth()->user()->isAdmin() ? '/role-selection' : '/attendance');
    }
    return view('auth.login');
})->name('login');

Route::post('/login', [AuthController::class, 'login']);
Route::any('/logout', [AuthController::class, 'logout'])->name('logout');

Route::get('/csrf-token', function () {
    return response()->json([
        'token' => csrf_token(),
        'authenticated' => auth()->check(),
    ]);
})->name('csrf.token');

// Onboarding Routes
Route::middleware('auth')->group(function () {
    Route::get('/onboarding/password', [OnboardingController::class, 'showPasswordForm'])->name('onboarding.password');
    Route::post('/onboarding/password', [OnboardingController::class, 'updatePassword'])->name('onboarding.password.update');
});

Route::middleware(['auth', 'password.changed'])->group(function () {
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
    Route::get('/attendance/history', [AttendanceController::class, 'history'])->name('attendance.history');
    Route::get('/attendance/profile', [AttendanceController::class, 'profile'])->name('attendance.profile');
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
        
        Route::get('breaks/export', [BreakController::class, 'export'])->name('breaks.export');
        Route::get('breaks', [BreakController::class, 'index'])->name('breaks.index');

        // Absences
        Route::get('absences', [\App\Http\Controllers\Admin\AbsenceController::class, 'index'])->name('absences.index');

        // Settings
        Route::get('settings', [\App\Http\Controllers\Admin\SettingController::class, 'index'])->name('settings.index');
        Route::post('settings', [\App\Http\Controllers\Admin\SettingController::class, 'update'])->name('settings.update');

        // Notifications
        Route::post('notifications/clear', function() {
            auth()->user()->notifications()->delete();
            return back();
        })->name('notifications.clear');

        Route::get('notifications/{id}/read', function($id) {
            $notification = auth()->user()->notifications()->findOrFail($id);
            $notification->markAsRead();
            return redirect(request('redirect_to', route('admin.dashboard')));
        })->name('notifications.read');

        // Profile
        Route::get('profile', [\App\Http\Controllers\Admin\ProfileController::class, 'index'])->name('profile.index');
        Route::put('profile', [\App\Http\Controllers\Admin\ProfileController::class, 'update'])->name('profile.update');
    });
});
