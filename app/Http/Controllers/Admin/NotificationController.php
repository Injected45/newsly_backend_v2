<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\BroadcastNotificationRequest;
use App\Models\PushNotification;
use App\Services\NotificationService;
use Illuminate\Http\Request;

class NotificationController extends Controller
{
    public function __construct(
        private NotificationService $notificationService
    ) {}

    public function index(Request $request)
    {
        $query = PushNotification::with(['user:id,name,email', 'article:id,title']);

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $notifications = $query->latest()->paginate(50);

        return view('admin.notifications.index', compact('notifications'));
    }

    public function create()
    {
        return view('admin.notifications.create');
    }

    public function broadcast(BroadcastNotificationRequest $request)
    {
        $data = $request->validated();

        $result = $this->notificationService->broadcast(
            $data['title'],
            $data['body'],
            $data['filter'] ?? [],
            $data['data'] ?? []
        );

        if (!empty($result['errors'])) {
            return back()->with('error', 'حدث خطأ: ' . implode(', ', $result['errors']));
        }

        return redirect()->route('admin.notifications.index')
            ->with('success', "تم إرسال الإشعار إلى {$result['total_sent']} جهاز");
    }
}


