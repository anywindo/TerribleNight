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
use App\Http\Controllers\Auth\ForgotPasswordController;
use App\Http\Controllers\Auth\ResetPasswordController;

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

// Password Reset Routes
Route::middleware('guest')->group(function () {
    Route::get('/forgot-password', [ForgotPasswordController::class, 'showLinkRequestForm'])->name('password.request');
    Route::post('/forgot-password', [ForgotPasswordController::class, 'sendResetLinkEmail'])->name('password.email')->middleware('throttle:3,15');
    Route::get('/reset-password/{token}', [ResetPasswordController::class, 'showResetForm'])->name('password.reset');
    Route::post('/reset-password', [ResetPasswordController::class, 'reset'])->name('password.update')->middleware('throttle:5,15');
});

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
    Route::put('/attendance/profile', [AttendanceController::class, 'updateProfile'])->name('attendance.profile.update');
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
        Route::match(['get', 'post'], 'notifications/clear', function() {
            auth()->user()->notifications()->delete();
            return back();
        })->name('notifications.clear');

        Route::get('notifications/{id}/read', function($id) {
            $notification = auth()->user()->notifications()->find($id);
            if ($notification) {
                $notification->markAsRead();
            }

            if (request()->filled('redirect_to')) {
                return redirect(request('redirect_to'));
            }

            $employeeName = null;
            if ($notification && isset($notification->data['employee_id'])) {
                $employeeName = \App\Models\User::find($notification->data['employee_id'])?->name;
            }
            if (!$employeeName && $notification && isset($notification->data['message'])) {
                $parts = explode(' terlambat ', $notification->data['message']);
                $employeeName = $parts[0] ?? null;
            }

            if ($employeeName) {
                return redirect()->route('admin.attendances.index', ['search' => trim($employeeName)]);
            }

            return redirect()->route('admin.attendances.index');
        })->name('notifications.read');

        // Profile
        Route::get('profile', [\App\Http\Controllers\Admin\ProfileController::class, 'index'])->name('profile.index');
        Route::put('profile', [\App\Http\Controllers\Admin\ProfileController::class, 'update'])->name('profile.update');
    });
});

// Serve storage files (supports serverless /tmp, static files, database cache, and fallbacks)
Route::get('/storage/{path}', function ($path) {
    abort_if(str_contains($path, '..'), 400);

    $filename = basename($path);
    $ext = strtolower(pathinfo($filename, PATHINFO_EXTENSION));
    $defaultMime = match($ext) {
        'webp' => 'image/webp',
        'png' => 'image/png',
        'gif' => 'image/gif',
        'svg' => 'image/svg+xml',
        default => 'image/jpeg',
    };

    $respondImage = function ($content, $mime) {
        return response($content, 200)
            ->header('Content-Type', $mime)
            ->header('Cache-Control', 'public, max-age=86400');
    };
    
    // 1. Check in /tmp/storage/app/public/ (local lambda container cache)
    $tmpPath = '/tmp/storage/app/public/' . $path;
    if (file_exists($tmpPath) && is_file($tmpPath)) {
        return $respondImage(file_get_contents($tmpPath), $defaultMime);
    }

    // 2. Fetch directly from Cloudflare R2 bucket (fast object storage)
    try {
        if (Storage::disk('r2')->exists($path)) {
            $content = Storage::disk('r2')->get($path);
            @mkdir(dirname($tmpPath), 0777, true);
            @file_put_contents($tmpPath, $content);
            return $respondImage($content, $defaultMime);
        }
    } catch (\Throwable $e) {}

    // 3. Check in public/
    $publicFile = public_path($path);
    if (file_exists($publicFile) && is_file($publicFile)) {
        return $respondImage(file_get_contents($publicFile), $defaultMime);
    }

    // 4. Check in public/selfies/ or public/avatars/
    $publicSelfie = public_path('selfies/' . $filename);
    if (file_exists($publicSelfie) && is_file($publicSelfie)) {
        return $respondImage(file_get_contents($publicSelfie), $defaultMime);
    }
    $publicAvatar = public_path('avatars/' . $filename);
    if (file_exists($publicAvatar) && is_file($publicAvatar)) {
        return $respondImage(file_get_contents($publicAvatar), $defaultMime);
    }

    // 5. Check database for avatar base64 (fallback & sync to R2)
    if (str_contains($path, 'avatar') || str_starts_with($path, 'avatars')) {
        $user = \App\Models\User::where('avatar', 'like', "%{$filename}%")->first();
        if ($user && !empty($user->avatar_base64)) {
            $binary = base64_decode($user->avatar_base64);
            @mkdir(dirname($tmpPath), 0777, true);
            @file_put_contents($tmpPath, $binary);
            try { Storage::disk('r2')->put($path, $binary, ['visibility' => 'public', 'ContentType' => $defaultMime]); } catch (\Throwable $e) {}
            return $respondImage($binary, $defaultMime);
        }
        abort(404);
    }

    // 6. Check database for selfie base64 (fallback & sync to R2)
    if (str_contains($path, 'selfie') || str_contains($path, '.webp')) {
        $event = \App\Models\AttendanceEvent::where('selfie_path', 'like', "%{$filename}%")->first();
        if ($event && !empty($event->selfie_base64)) {
            $binary = base64_decode($event->selfie_base64);
            @mkdir(dirname($tmpPath), 0777, true);
            @file_put_contents($tmpPath, $binary);
            try { Storage::disk('r2')->put($path, $binary, ['visibility' => 'public', 'ContentType' => 'image/webp']); } catch (\Throwable $e) {}
            return $respondImage($binary, 'image/webp');
        }
    }

    abort(404);
})->where('path', '.*');

