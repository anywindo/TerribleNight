<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Facades\Hash;

class EmployeeController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = User::with(['roles', 'location']);
        
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhere('nik', 'like', "%{$search}%");
            });
        }

        $employees = $query->paginate(10)->appends($request->all());
        return view('admin.employees.index', compact('employees'));
    }

    public function create()
    {
        $roles = Role::where('guard_name', 'web')->get();
        $locations = \App\Models\Location::orderBy('name')->get();
        return view('admin.employees.create', compact('roles', 'locations'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'nik' => 'nullable|string|max:50|unique:users',
            'password' => 'required|string|min:8|confirmed',
            'location_id' => 'nullable|exists:locations,id',
        ]);

        $employee = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'nik' => $request->nik,
            'password' => Hash::make($request->password),
            'location_id' => $request->location_id,
            'is_active' => $request->has('is_active'),
        ]);

        if($request->has('role')) {
            $employee->syncRoles([$request->role]);
        }

        return redirect()->route('admin.employees.index')->with('success', 'Employee created successfully.');
    }

    public function edit(User $employee)
    {
        $roles = Role::where('guard_name', 'web')->get();
        $locations = \App\Models\Location::orderBy('name')->get();
        return view('admin.employees.edit', compact('employee', 'roles', 'locations'));
    }

    public function update(Request $request, User $employee)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email,'.$employee->id,
            'nik' => 'nullable|string|max:50|unique:users,nik,'.$employee->id,
            'location_id' => 'nullable|exists:locations,id',
        ]);

        $data = $request->only(['name', 'email', 'nik', 'location_id']);
        $data['is_active'] = $request->has('is_active');
        
        if ($request->filled('password')) {
            $data['password'] = Hash::make($request->password);
        }

        $employee->update($data);

        if($request->has('role')) {
            $employee->syncRoles([$request->role]);
        }

        return redirect()->route('admin.employees.index')->with('success', 'Employee updated successfully.');
    }

    public function destroy(User $employee)
    {
        if ($employee->id == auth()->id()) {
            return redirect()->route('admin.employees.index')->with('error', 'Cannot delete yourself.');
        }
        $employee->delete();
        return redirect()->route('admin.employees.index')->with('success', 'Employee deleted successfully.');
    }
}
