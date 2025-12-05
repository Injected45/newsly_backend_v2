<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\PushNotification;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class NotificationController extends Controller
{
    /**
     * List user notifications
     */
    public function index(Request $request): JsonResponse
    {
        $request->validate([
            'per_page' => ['nullable', 'integer', 'min:1', 'max:50'],
            'unread_only' => ['nullable', 'boolean'],
        ]);

        $query = $request->user()
            ->pushNotifications()
            ->with('article:id,title,image_url')
            ->latest();

        if ($request->boolean('unread_only')) {
            $query->whereIn('status', [PushNotification::STATUS_SENT, PushNotification::STATUS_PENDING]);
        }

        $perPage = $request->integer('per_page', 20);
        $notifications = $query->paginate($perPage);

        return $this->paginated($notifications);
    }

    /**
     * Get unread notifications count
     */
    public function unreadCount(Request $request): JsonResponse
    {
        $count = $request->user()
            ->pushNotifications()
            ->whereIn('status', [PushNotification::STATUS_SENT, PushNotification::STATUS_PENDING])
            ->count();

        return $this->success(['count' => $count]);
    }

    /**
     * Mark notification as delivered/read
     */
    public function markRead(Request $request, PushNotification $notification): JsonResponse
    {
        if ($notification->user_id !== $request->user()->id) {
            return $this->error('Unauthorized', 403);
        }

        $notification->markAsDelivered();

        return $this->success(null, 'Notification marked as read');
    }

    /**
     * Mark all notifications as read
     */
    public function markAllRead(Request $request): JsonResponse
    {
        $request->user()
            ->pushNotifications()
            ->whereIn('status', [PushNotification::STATUS_SENT, PushNotification::STATUS_PENDING])
            ->update([
                'status' => PushNotification::STATUS_DELIVERED,
                'delivered_at' => now(),
            ]);

        return $this->success(null, 'All notifications marked as read');
    }
}



