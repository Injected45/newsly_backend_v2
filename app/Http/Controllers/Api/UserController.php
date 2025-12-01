<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\RegisterDeviceRequest;
use App\Http\Requests\Api\UpdateProfileRequest;
use App\Http\Resources\UserResource;
use App\Models\UserDevice;
use App\Services\NotificationService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class UserController extends Controller
{
    public function __construct(
        private NotificationService $notificationService
    ) {}

    /**
     * Update user profile
     */
    public function update(UpdateProfileRequest $request): JsonResponse
    {
        $user = $request->user();
        $data = $request->validated();

        // Handle settings merge
        if (isset($data['settings'])) {
            $data['settings'] = array_merge($user->settings ?? [], $data['settings']);
        }

        $user->update($data);

        return $this->success(
            new UserResource($user->fresh()->load('country')),
            'Profile updated successfully'
        );
    }

    /**
     * Register FCM device token
     */
    public function registerDevice(RegisterDeviceRequest $request): JsonResponse
    {
        $user = $request->user();
        $data = $request->validated();

        $device = UserDevice::updateOrCreateToken(
            $user->id,
            $data['platform'],
            $data['fcm_token'],
            array_filter([
                'device_id' => $data['device_id'] ?? null,
                'device_name' => $data['device_name'] ?? null,
                'device_model' => $data['device_model'] ?? null,
                'os_version' => $data['os_version'] ?? null,
                'app_version' => $data['app_version'] ?? null,
            ])
        );

        // Subscribe to user's topics
        $this->subscribeToUserTopics($user, $data['fcm_token']);

        return $this->success([
            'device_id' => $device->id,
            'registered' => true,
        ], 'Device registered successfully');
    }

    /**
     * Unregister device
     */
    public function unregisterDevice(Request $request): JsonResponse
    {
        $request->validate([
            'fcm_token' => ['required', 'string'],
        ]);

        UserDevice::where('user_id', $request->user()->id)
            ->where('fcm_token', $request->fcm_token)
            ->update(['is_active' => false]);

        return $this->success(null, 'Device unregistered successfully');
    }

    /**
     * Get user notification settings
     */
    public function notificationSettings(Request $request): JsonResponse
    {
        $user = $request->user();

        return $this->success([
            'notifications_enabled' => $user->getSetting('notifications_enabled', true),
            'breaking_only' => $user->getSetting('breaking_only', false),
            'dnd_start' => $user->getSetting('dnd_start'),
            'dnd_end' => $user->getSetting('dnd_end'),
        ]);
    }

    /**
     * Update notification settings
     */
    public function updateNotificationSettings(Request $request): JsonResponse
    {
        $request->validate([
            'notifications_enabled' => ['sometimes', 'boolean'],
            'breaking_only' => ['sometimes', 'boolean'],
            'dnd_start' => ['sometimes', 'nullable', 'date_format:H:i'],
            'dnd_end' => ['sometimes', 'nullable', 'date_format:H:i'],
        ]);

        $user = $request->user();
        $settings = $user->settings ?? [];

        foreach (['notifications_enabled', 'breaking_only', 'dnd_start', 'dnd_end'] as $key) {
            if ($request->has($key)) {
                $settings[$key] = $request->input($key);
            }
        }

        $user->update(['settings' => $settings]);

        return $this->success($settings, 'Notification settings updated');
    }

    /**
     * Subscribe device to user's topics based on subscriptions
     */
    private function subscribeToUserTopics($user, string $fcmToken): void
    {
        foreach ($user->subscriptions as $subscription) {
            $topic = $subscription->getTopicName();
            if ($topic) {
                $this->notificationService->subscribeToTopic($fcmToken, $topic);
            }
        }
    }
}


