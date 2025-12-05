<?php

namespace App\Services;

use App\Models\User;
use Exception;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class GoogleAuthService
{
    private array $validClientIds;
    private string $tokenInfoUrl;

    public function __construct()
    {
        $this->validClientIds = array_filter([
            config('services.google.client_id'),
            config('services.google.mobile_client_ids.android'),
            config('services.google.mobile_client_ids.ios'),
        ]);

        $this->tokenInfoUrl = config('services.google.token_info_url');
    }

    /**
     * Verify Google ID Token and return user data
     */
    public function verifyIdToken(string $idToken): ?array
    {
        try {
            $response = Http::timeout(10)
                ->get($this->tokenInfoUrl, [
                    'id_token' => $idToken,
                ]);

            if (!$response->successful()) {
                Log::warning('Google token verification failed', [
                    'status' => $response->status(),
                    'body' => $response->body(),
                ]);
                return null;
            }

            $tokenData = $response->json();

            // Validate the token
            if (!$this->validateTokenData($tokenData)) {
                return null;
            }

            return [
                'google_id' => $tokenData['sub'],
                'email' => $tokenData['email'] ?? null,
                'email_verified' => ($tokenData['email_verified'] ?? 'false') === 'true',
                'name' => $tokenData['name'] ?? null,
                'given_name' => $tokenData['given_name'] ?? null,
                'family_name' => $tokenData['family_name'] ?? null,
                'picture' => $tokenData['picture'] ?? null,
                'locale' => $tokenData['locale'] ?? null,
            ];
        } catch (Exception $e) {
            Log::error('Google token verification exception', [
                'error' => $e->getMessage(),
            ]);
            return null;
        }
    }

    /**
     * Validate token data
     */
    private function validateTokenData(array $tokenData): bool
    {
        // Check if audience matches our client IDs
        $audience = $tokenData['aud'] ?? '';
        if (!in_array($audience, $this->validClientIds)) {
            Log::warning('Google token audience mismatch', [
                'received' => $audience,
                'expected' => $this->validClientIds,
            ]);
            return false;
        }

        // Check issuer
        $validIssuers = ['accounts.google.com', 'https://accounts.google.com'];
        $issuer = $tokenData['iss'] ?? '';
        if (!in_array($issuer, $validIssuers)) {
            Log::warning('Google token issuer mismatch', [
                'received' => $issuer,
            ]);
            return false;
        }

        // Check expiration
        $exp = $tokenData['exp'] ?? 0;
        if ($exp < time()) {
            Log::warning('Google token expired', [
                'exp' => $exp,
                'now' => time(),
            ]);
            return false;
        }

        // Ensure we have required fields
        if (empty($tokenData['sub'])) {
            Log::warning('Google token missing sub field');
            return false;
        }

        return true;
    }

    /**
     * Find or create user from Google data
     */
    public function findOrCreateUser(array $googleData, string $ip): User
    {
        // First try to find by google_id
        $user = User::findByGoogleId($googleData['google_id']);

        if ($user) {
            // Update user info if needed
            $user->update([
                'name' => $googleData['name'] ?? $user->name,
                'avatar' => $googleData['picture'] ?? $user->avatar,
            ]);

            $user->updateLastLogin($ip);
            return $user;
        }

        // If email is verified, try to find existing user by email
        if ($googleData['email_verified'] && !empty($googleData['email'])) {
            $user = User::findByEmail($googleData['email']);

            if ($user) {
                // Link Google account to existing user
                $user->update([
                    'google_id' => $googleData['google_id'],
                    'name' => $googleData['name'] ?? $user->name,
                    'avatar' => $googleData['picture'] ?? $user->avatar,
                    'email_verified_at' => $user->email_verified_at ?? now(),
                ]);

                $user->updateLastLogin($ip);
                return $user;
            }
        }

        // Create new user
        $user = User::create([
            'google_id' => $googleData['google_id'],
            'email' => $googleData['email_verified'] ? $googleData['email'] : null,
            'name' => $googleData['name'] ?? 'User',
            'avatar' => $googleData['picture'],
            'email_verified_at' => $googleData['email_verified'] ? now() : null,
            'language' => $googleData['locale'] ?? 'ar',
            'last_login_at' => now(),
            'last_login_ip' => $ip,
        ]);

        return $user;
    }

    /**
     * Create Sanctum token for user
     */
    public function createToken(User $user, string $deviceName = 'mobile'): string
    {
        // Revoke old tokens if needed (optional - keep last 5)
        $tokens = $user->tokens()->orderBy('created_at', 'desc')->get();
        if ($tokens->count() > 5) {
            $tokens->slice(5)->each->delete();
        }

        return $user->createToken($deviceName, ['*'])->plainTextToken;
    }
}



