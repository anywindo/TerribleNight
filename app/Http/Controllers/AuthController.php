<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    public function login(Request $request)
    {
        $request->validate([
            'email' => ['required_without:login', 'string'],
            'login' => ['required_without:email', 'string'],
            'password' => ['required', 'string'],
        ], [
            'email.required_without' => 'Email atau ID Karyawan wajib diisi.',
            'login.required_without' => 'Email atau ID Karyawan wajib diisi.',
            'password.required' => 'Kata sandi wajib diisi.',
        ]);

        $login = $request->input('login') 
            ?? $request->input('email') 
            ?? $request->input('employee_id') 
            ?? $request->input('username');

        $user = \App\Models\User::findByLogin($login);

        $credentials = [
            'email' => $user ? $user->email : $login,
            'password' => $request->password,
        ];

        if (Auth::attempt($credentials, $request->boolean('remember'))) {
            $request->session()->regenerate();

            $authenticatedUser = Auth::user();

            if (!$authenticatedUser->is_active) {
                Auth::logout();
                $request->session()->invalidate();
                $request->session()->regenerateToken();

                return back()->withErrors([
                    'email' => 'Akun Anda dinonaktifkan. Silakan hubungi admin.',
                ])->onlyInput('email', 'login');
            }

            // Onboarding password change if user still has default password
            if ($authenticatedUser->mustChangePassword()) {
                return redirect()->route('onboarding.password');
            }

            // Skip role selection for super-admin
            if ($authenticatedUser->isSuperAdmin()) {
                return redirect()->intended('/admin/dashboard');
            }

            // Redirect based on role
            if ($authenticatedUser->isAdmin()) {
                return redirect()->intended('/role-selection');
            }

            return redirect()->intended('/attendance');
        }

        return back()->withErrors([
            'email' => 'Email / ID Karyawan atau kata sandi salah.',
        ])->onlyInput('email', 'login');
    }

    public function logout(Request $request)
    {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/');
    }
}
