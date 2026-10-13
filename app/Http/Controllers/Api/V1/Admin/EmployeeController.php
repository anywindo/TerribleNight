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
            'employee_id' => 'nullable|string|max:50|unique:users,employee_id',
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'nik' => 'required|string|unique:users,nik|max:255',
            'dob' => 'nullable|date',
            'phone' => 'required|string|max:255',
            'location_id' => 'required|exists:locations,id',
            'avatar' => 'nullable|image|max:2048', // 2MB max
            'password' => 'nullable|string|min:8|confirmed',
        ]);

        $dob = $validated['dob'] ?? null;
        if (!$dob && !empty($validated['nik'])) {
            $dob = User::parseDobDateFromNik($validated['nik']);
        }

        $passwordString = !empty($validated['password'])
            ? $validated['password']
            : User::computeDefaultPassword($dob, $validated['nik'] ?? null);

        $avatarPath = null;
        $avatarBase64 = null;
        if ($request->hasFile('avatar')) {
            $uploaded = $fileUploadService->uploadImageWithBase64($request->file('avatar'), 'avatars');
            $avatarPath = $uploaded['path'];
            $avatarBase64 = $uploaded['base64'];
        }

        $userData = [
            'name' => $validated['name'],
            'email' => $validated['email'],
            'nik' => $validated['nik'],
            'dob' => $dob,
            'phone' => $validated['phone'],
            'location_id' => $validated['location_id'],
            'avatar' => $avatarPath,
            'avatar_base64' => $avatarBase64,
            'password' => \Illuminate\Support\Facades\Hash::make($passwordString),
        ];

        if (!empty($validated['employee_id'])) {
            $userData['employee_id'] = $validated['employee_id'];
        }

        $user = User::create($userData);

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
            'employee_id' => 'nullable|string|max:50|unique:users,employee_id,' . $employee->id,
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $employee->id,
            'nik' => 'required|string|max:255|unique:users,nik,' . $employee->id,
            'dob' => 'nullable|date',
            'phone' => 'required|string|max:255',
            'location_id' => 'required|exists:locations,id',
            'avatar' => 'nullable|image|max:2048',
            'password' => 'nullable|string|min:8|confirmed',
        ]);

        if (array_key_exists('dob', $validated) && empty($validated['dob']) && !empty($validated['nik'])) {
            $validated['dob'] = User::parseDobDateFromNik($validated['nik']);
        }

        if ($request->hasFile('avatar')) {
            $uploaded = $fileUploadService->uploadImageWithBase64($request->file('avatar'), 'avatars');
            $validated['avatar'] = $uploaded['path'];
            $validated['avatar_base64'] = $uploaded['base64'];
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
