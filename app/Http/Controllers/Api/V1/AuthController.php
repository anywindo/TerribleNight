<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Requests\Auth\LoginRequest;
use App\Http\Requests\Auth\RegisterRequest;
use App\Http\Resources\Auth\UserResource;
use App\Services\AuthService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;

class AuthController extends BaseController
{
    public function __construct(private readonly AuthService $authService) {}

    public function register(RegisterRequest $request): JsonResponse
    {
        $result = $this->authService->register($request->validated());

        return $this->created([
            'user'       => new UserResource($result['user']),
            'token'      => $result['token'],
            'token_type' => $result['token_type'],
            'expires_in' => $result['expires_in'],
        ], 'Registration successful');
    }

    public function login(LoginRequest $request): JsonResponse
    {
        $result = $this->authService->login($request->validated());

        return $this->success([
            'user'       => new UserResource($result['user']),
            'token'      => $result['token'],
            'token_type' => $result['token_type'],
            'expires_in' => $result['expires_in'],
        ], 'Login successful');
    }

    public function logout(Request $request): JsonResponse
    {
        $this->authService->logout($request->user());

        return $this->noContent('Logged out successfully');
    }

    public function refresh(Request $request): JsonResponse
    {
        $result = $this->authService->refresh($request->user());

        return $this->success($result, 'Token refreshed');
    }

    public function profile(Request $request): JsonResponse
    {
        return $this->success(
            new UserResource($request->user()->load('roles', 'location')),
            'Profile retrieved'
        );
    }

    public function updateOnboardingPassword(Request $request): JsonResponse
    {
        $user = $request->user();

        $request->validate([
            'password' => [
                'required',
                'string',
                'confirmed',
                Password::min(8)
                    ->letters()
                    ->mixedCase()
                    ->numbers(),
            ],
        ], [
            'password.required' => 'Kata sandi baru wajib diisi.',
            'password.min' => 'Kata sandi baru minimal 8 karakter.',
            'password.confirmed' => 'Konfirmasi kata sandi tidak cocok.',
            'password.letters' => 'Kata sandi harus mengandung huruf.',
            'password.mixed_case' => 'Kata sandi harus mengandung huruf besar dan kecil.',
            'password.numbers' => 'Kata sandi harus mengandung angka.',
        ]);

        $defaultPwd = $user->getDefaultPassword();

        if ($request->password === $defaultPwd || strtolower($request->password) === 'password') {
            return response()->json([
                'meta' => [
                    'success' => false,
                    'message' => 'Kata sandi baru tidak boleh sama dengan kata sandi default atau tanggal lahir Anda.',
                ]
            ], 422);
        }

        $user->password = Hash::make($request->password);
        $user->password_changed_at = now();
        $user->save();

        return $this->success(
            new UserResource($user->fresh()->load('roles', 'location')),
            'Password updated successfully'
        );
    }
}
