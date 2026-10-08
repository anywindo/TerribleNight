<?php

use App\Http\Controllers\Api\V1\AuthController;
use App\Http\Controllers\Api\V1\TeamController;
use Illuminate\Support\Facades\Route;

/*
Route::get('health', fn () => response()->json(['status' => 'ok', 'timestamp' => now()]));

Route::prefix('v1')->group(function () {

    // Auth (Rate limited to 5/min)
    Route::prefix('auth')->middleware('throttle:auth')->group(function () {
        Route::post('login', [AuthController::class, 'login']);
        Route::post('register', [AuthController::class, 'register']);
        
        Route::middleware('auth:sanctum')->group(function () {
            Route::get('profile', [AuthController::class, 'profile']);
            Route::post('logout', [AuthController::class, 'logout']);
            Route::post('refresh', [AuthController::class, 'refresh']);
        });
    });

    // Protected API Routes
    Route::middleware(['auth:sanctum', 'throttle:api'])->group(function () {
        // Employee Routes
        Route::prefix('employee')->group(function () {
            Route::get('attendance/history', [\App\Http\Controllers\Api\V1\Employee\AttendanceController::class, 'history']);
            Route::post('attendance/events', [\App\Http\Controllers\Api\V1\Employee\AttendanceController::class, 'storeEvent']);
            
            Route::get('correction-requests', [\App\Http\Controllers\Api\V1\Employee\AttendanceCorrectionRequestController::class, 'index']);
            Route::post('correction-requests', [\App\Http\Controllers\Api\V1\Employee\AttendanceCorrectionRequestController::class, 'store']);
        });

        // Admin/HR Routes
        Route::prefix('admin')->middleware('role:admin|super-admin|HR')->group(function () {
            Route::apiResource('employees', \App\Http\Controllers\Api\V1\Admin\EmployeeController::class);
            
            Route::get('attendances', [\App\Http\Controllers\Api\V1\Admin\AttendanceController::class, 'index']);
            
            Route::get('correction-requests', [\App\Http\Controllers\Api\V1\Admin\AttendanceCorrectionRequestController::class, 'index']);
            Route::put('correction-requests/{id}/process', [\App\Http\Controllers\Api\V1\Admin\AttendanceCorrectionRequestController::class, 'process']);
        });

        // Teams
        Route::post('teams/{team}/members', [TeamController::class, 'addMember']);
        Route::delete('teams/{team}/members/{user}', [TeamController::class, 'removeMember']);
        Route::apiResource('teams', TeamController::class);



        // Time Entries

        // Reports

    });
});
*/
