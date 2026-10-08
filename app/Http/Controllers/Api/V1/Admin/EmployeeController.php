<?php

namespace App\Http\Controllers\Api\V1\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Services\FileUploadService;
use Illuminate\Http\Request;

class EmployeeController extends Controller
{
    public function index()
    {
        $employees = User::role('Employee')->get();
        return response()->json([
            'message' => 'Employees retrieved successfully.',
            'data' => $employees
        ]);
    }

    public function store(Request $request, FileUploadService $fileUploadService)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'nik' => 'required|string|unique:users,nik|max:255',
            'phone' => 'required|string|max:255',
            'avatar' => 'nullable|image|max:2048', // 2MB max
            'password' => 'required|string|min:8|confirmed',
        ]);

        $avatarPath = null;
        if ($request->hasFile('avatar')) {
            $avatarPath = $fileUploadService->uploadImage($request->file('avatar'), 'avatars');
        }

        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'nik' => $validated['nik'],
            'phone' => $validated['phone'],
            'avatar' => $avatarPath,
            'password' => \Illuminate\Support\Facades\Hash::make($validated['password']),
        ]);

        $user->assignRole('Employee');

        return response()->json([
            'message' => 'Employee created successfully.',
            'data' => $user
        ], 201);
    }

    public function show(User $employee)
    {
        return response()->json([
            'message' => 'Employee retrieved successfully.',
            'data' => $employee
        ]);
    }

    public function update(Request $request, User $employee, FileUploadService $fileUploadService)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $employee->id,
            'nik' => 'required|string|max:255|unique:users,nik,' . $employee->id,
            'phone' => 'required|string|max:255',
            'avatar' => 'nullable|image|max:2048',
            'password' => 'nullable|string|min:8|confirmed',
        ]);

        if ($request->hasFile('avatar')) {
            $validated['avatar'] = $fileUploadService->uploadImage($request->file('avatar'), 'avatars');
        } else {
            unset($validated['avatar']);
        }

        if (!empty($validated['password'])) {
            $validated['password'] = \Illuminate\Support\Facades\Hash::make($validated['password']);
        } else {
            unset($validated['password']);
        }

        $employee->update($validated);

        return response()->json([
            'message' => 'Employee updated successfully.',
            'data' => $employee
        ]);
    }

    public function destroy(User $employee)
    {
        $employee->delete();
        return response()->json([
            'message' => 'Employee deleted successfully.'
        ]);
    }
}
