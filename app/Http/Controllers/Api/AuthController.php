<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\LoginRequest;
use App\Http\Requests\Api\RegisterRequest;
use App\Http\Resources\UserResource;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    /**
     * Register a new user
     * 
     * @bodyParam name string required User's full name. Example: أحمد محمد
     * @bodyParam email string required User's email address. Example: user@example.com
     * @bodyParam password string required Password (min 8 chars). Example: password123
     * @bodyParam password_confirmation string required Confirm password. Example: password123
     * @bodyParam country_id int optional Country ID. Example: 1
     */
    public function register(RegisterRequest $request): JsonResponse
    {
        $validated = $request->validated();

        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
            'country_id' => $validated['country_id'] ?? null,
            'last_login_at' => now(),
            'last_login_ip' => $request->ip(),
        ]);

        // Create Sanctum token
        $token = $user->createToken('mobile', ['*'])->plainTextToken;

        return $this->success([
            'token' => $token,
            'user' => new UserResource($user->load('country')),
        ], 'Registration successful', 201);
    }

    /**
     * Login with email and password
     * 
     * @bodyParam email string required User's email address. Example: user@example.com
     * @bodyParam password string required User's password. Example: password123
     */
    public function login(LoginRequest $request): JsonResponse
    {
        $validated = $request->validated();

        $user = User::where('email', $validated['email'])->first();

        if (!$user || !Hash::check($validated['password'], $user->password)) {
            return $this->error('البريد الإلكتروني أو كلمة المرور غير صحيحة', 401);
        }

        // Update last login info
        $user->update([
            'last_login_at' => now(),
            'last_login_ip' => $request->ip(),
        ]);

        // Create Sanctum token
        $token = $user->createToken('mobile', ['*'])->plainTextToken;

        return $this->success([
            'token' => $token,
            'user' => new UserResource($user->load('country')),
        ], 'Login successful');
    }

    /**
     * Get current user profile
     */
    public function me(Request $request): JsonResponse
    {
        $user = $request->user()->load([
            'country',
            'subscriptions.source',
            'subscriptions.category',
            'subscriptions.country',
        ]);

        return $this->success(new UserResource($user));
    }

    /**
     * Logout - revoke current token
     */
    public function logout(Request $request): JsonResponse
    {
        // Revoke current token
        $request->user()->currentAccessToken()->delete();

        return $this->success(null, 'Logged out successfully');
    }

    /**
     * Logout from all devices - revoke all tokens
     */
    public function logoutAll(Request $request): JsonResponse
    {
        // Revoke all tokens
        $request->user()->tokens()->delete();

        return $this->success(null, 'Logged out from all devices');
    }

    /**
     * Update user password
     * 
     * @bodyParam current_password string required Current password. Example: oldpassword123
     * @bodyParam password string required New password (min 8 chars). Example: newpassword123
     * @bodyParam password_confirmation string required Confirm new password. Example: newpassword123
     */
    public function updatePassword(Request $request): JsonResponse
    {
        $request->validate([
            'current_password' => ['required', 'string'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ]);

        $user = $request->user();

        if (!Hash::check($request->current_password, $user->password)) {
            return $this->error('كلمة المرور الحالية غير صحيحة', 422);
        }

        $user->update([
            'password' => Hash::make($request->password),
        ]);

        return $this->success(null, 'تم تغيير كلمة المرور بنجاح');
    }
}
