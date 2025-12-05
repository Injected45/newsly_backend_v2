<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\CompleteSetupRequest;
use App\Http\Resources\UserResource;
use App\Models\UserSubscription;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class SetupController extends Controller
{
    /**
     * Check if user needs to complete setup
     */
    public function checkSetup(Request $request): JsonResponse
    {
        $user = $request->user();
        
        return $this->success([
            'needs_setup' => !$user->hasCompletedSetup(),
            'setup_completed_at' => $user->setup_completed_at?->toISOString(),
        ]);
    }

    /**
     * Complete initial user setup
     * 
     * This endpoint is called after user registration to set:
     * - Country preference
     * - Initial source subscriptions
     */
    public function completeSetup(CompleteSetupRequest $request): JsonResponse
    {
        $user = $request->user();
        $validated = $request->validated();

        // Check if setup already completed
        if ($user->hasCompletedSetup()) {
            return $this->error('تم إكمال الإعداد مسبقاً', 400);
        }

        // Update user country
        $user->update([
            'country_id' => $validated['country_id'],
        ]);

        // Create subscriptions for selected sources
        foreach ($validated['source_ids'] as $sourceId) {
            UserSubscription::firstOrCreate([
                'user_id' => $user->id,
                'source_id' => $sourceId,
            ], [
                'notify_breaking' => true,
                'notify_new_articles' => true,
            ]);
        }

        // Mark setup as completed
        $user->completeSetup();

        // Reload user with relationships
        $user->load(['country', 'subscriptions.source']);

        return $this->success([
            'user' => new UserResource($user),
            'message' => 'تم إكمال الإعداد بنجاح',
        ], 'Setup completed successfully');
    }

    /**
     * Skip setup (user can complete it later from settings)
     */
    public function skipSetup(Request $request): JsonResponse
    {
        $user = $request->user();

        // Mark setup as completed even if skipped
        // User can still change preferences from settings
        $user->completeSetup();

        return $this->success([
            'message' => 'تم تخطي الإعداد',
        ], 'Setup skipped');
    }
}



