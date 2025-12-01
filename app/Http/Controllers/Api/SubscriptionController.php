<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\SubscriptionRequest;
use App\Http\Resources\SubscriptionResource;
use App\Models\UserSubscription;
use App\Services\NotificationService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class SubscriptionController extends Controller
{
    public function __construct(
        private NotificationService $notificationService
    ) {}

    /**
     * List user subscriptions
     */
    public function index(Request $request): JsonResponse
    {
        $subscriptions = $request->user()
            ->subscriptions()
            ->with(['source', 'category', 'country'])
            ->get();

        return $this->success(SubscriptionResource::collection($subscriptions));
    }

    /**
     * Create subscription
     */
    public function store(SubscriptionRequest $request): JsonResponse
    {
        $user = $request->user();
        $data = $request->validated();

        // Check if subscription already exists
        $existing = $user->subscriptions()
            ->where('source_id', $data['source_id'] ?? null)
            ->where('category_id', $data['category_id'] ?? null)
            ->where('country_id', $data['country_id'] ?? null)
            ->first();

        if ($existing) {
            return $this->error('Already subscribed', 409);
        }

        $subscription = $user->subscriptions()->create([
            'source_id' => $data['source_id'] ?? null,
            'category_id' => $data['category_id'] ?? null,
            'country_id' => $data['country_id'] ?? null,
            'notifications_enabled' => $data['notifications_enabled'] ?? true,
        ]);

        // Subscribe to FCM topic
        $topic = $subscription->getTopicName();
        if ($topic) {
            foreach ($user->getActiveFcmTokens() as $token) {
                $this->notificationService->subscribeToTopic($token, $topic);
            }
        }

        $subscription->load(['source', 'category', 'country']);

        return $this->success(
            new SubscriptionResource($subscription),
            'Subscribed successfully',
            201
        );
    }

    /**
     * Update subscription
     */
    public function update(Request $request, UserSubscription $subscription): JsonResponse
    {
        // Ensure user owns the subscription
        if ($subscription->user_id !== $request->user()->id) {
            return $this->error('Unauthorized', 403);
        }

        $request->validate([
            'notifications_enabled' => ['required', 'boolean'],
        ]);

        $subscription->update([
            'notifications_enabled' => $request->boolean('notifications_enabled'),
        ]);

        return $this->success(
            new SubscriptionResource($subscription->fresh()->load(['source', 'category', 'country'])),
            'Subscription updated'
        );
    }

    /**
     * Delete subscription
     */
    public function destroy(Request $request, UserSubscription $subscription): JsonResponse
    {
        $user = $request->user();

        // Ensure user owns the subscription
        if ($subscription->user_id !== $user->id) {
            return $this->error('Unauthorized', 403);
        }

        // Unsubscribe from FCM topic
        $topic = $subscription->getTopicName();
        if ($topic) {
            foreach ($user->getActiveFcmTokens() as $token) {
                $this->notificationService->unsubscribeFromTopic($token, $topic);
            }
        }

        $subscription->delete();

        return $this->success(null, 'Unsubscribed successfully');
    }

    /**
     * Sync subscriptions (bulk update)
     */
    public function sync(Request $request): JsonResponse
    {
        $request->validate([
            'source_ids' => ['sometimes', 'array'],
            'source_ids.*' => ['exists:sources,id'],
            'category_ids' => ['sometimes', 'array'],
            'category_ids.*' => ['exists:categories,id'],
            'country_ids' => ['sometimes', 'array'],
            'country_ids.*' => ['exists:countries,id'],
        ]);

        $user = $request->user();

        // Remove old subscriptions
        $user->subscriptions()->delete();

        // Create new subscriptions
        $subscriptions = collect();

        foreach ($request->input('source_ids', []) as $sourceId) {
            $subscriptions->push($user->subscriptions()->create([
                'source_id' => $sourceId,
            ]));
        }

        foreach ($request->input('category_ids', []) as $categoryId) {
            $subscriptions->push($user->subscriptions()->create([
                'category_id' => $categoryId,
            ]));
        }

        foreach ($request->input('country_ids', []) as $countryId) {
            $subscriptions->push($user->subscriptions()->create([
                'country_id' => $countryId,
            ]));
        }

        return $this->success([
            'count' => $subscriptions->count(),
        ], 'Subscriptions synced');
    }
}


