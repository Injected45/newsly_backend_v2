<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\GoogleLoginRequest;
use App\Http\Resources\UserResource;
use App\Services\GoogleAuthService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class AuthController extends Controller
{
    public function __construct(
        private GoogleAuthService $googleAuth
    ) {}

    /**
     * Login with Google ID Token
     * 
     * @bodyParam id_token string required The Google ID Token from mobile app. Example: eyJhbGciOiJSUzI1NiIsInR5cCI6IkpXVCJ9...
     */
    public function google(GoogleLoginRequest $request): JsonResponse
    {
        $idToken = $request->validated('id_token');

        // Verify Google token
        $googleData = $this->googleAuth->verifyIdToken($idToken);

        if (!$googleData) {
            return $this->error('Invalid or expired Google token', 401);
        }

        // Check email verification for security
        if (!$googleData['email_verified'] && $googleData['email']) {
            return $this->error('Email not verified with Google', 401);
        }

        // Find or create user
        $user = $this->googleAuth->findOrCreateUser(
            $googleData,
            $request->ip()
        );

        // Create Sanctum token
        $token = $this->googleAuth->createToken($user, 'mobile');

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
}


