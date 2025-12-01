<?php

namespace App\Services;

use App\Models\Article;
use App\Models\PushNotification;
use App\Models\User;
use App\Models\UserDevice;
use App\Models\UserSubscription;
use Exception;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Log;
use Kreait\Firebase\Contract\Messaging;
use Kreait\Firebase\Messaging\CloudMessage;
use Kreait\Firebase\Messaging\Notification;

class NotificationService
{
    private ?Messaging $messaging;
    private int $batchSize;
    private int $retryCount;

    public function __construct()
    {
        try {
            $this->messaging = app('firebase.messaging');
        } catch (Exception $e) {
            $this->messaging = null;
            Log::warning('Firebase messaging not configured', ['error' => $e->getMessage()]);
        }

        $this->batchSize = config('news.notifications.batch_size', 500);
        $this->retryCount = config('news.notifications.retry_count', 3);
    }

    /**
     * Send notification for a new article
     */
    public function sendArticleNotification(Article $article): array
    {
        $results = [
            'total_sent' => 0,
            'total_failed' => 0,
            'errors' => [],
        ];

        if (!$this->messaging) {
            $results['errors'][] = 'Firebase messaging not configured';
            return $results;
        }

        // Get users subscribed to this article's source/category/country
        $tokens = $this->getTargetTokens($article);

        if ($tokens->isEmpty()) {
            return $results;
        }

        // Prepare notification
        $notification = Notification::create(
            $article->title,
            $article->summary ?? ''
        );

        $data = [
            'article_id' => (string) $article->id,
            'source_id' => (string) $article->source_id,
            'type' => 'new_article',
            'is_breaking' => $article->is_breaking ? '1' : '0',
            'click_action' => 'OPEN_ARTICLE',
        ];

        // Send in batches
        foreach ($tokens->chunk($this->batchSize) as $batch) {
            $batchTokens = $batch->pluck('fcm_token')->toArray();

            try {
                $message = CloudMessage::new()
                    ->withNotification($notification)
                    ->withData($data);

                $report = $this->messaging->sendMulticast($message, $batchTokens);

                $results['total_sent'] += $report->successes()->count();
                $results['total_failed'] += $report->failures()->count();

                // Handle invalid tokens
                $this->handleInvalidTokens($report, $batch);

                // Log notifications
                $this->logNotifications($batch, $article, $report);

            } catch (Exception $e) {
                $results['errors'][] = $e->getMessage();
                Log::error('FCM batch send failed', [
                    'article_id' => $article->id,
                    'error' => $e->getMessage(),
                ]);
            }
        }

        return $results;
    }

    /**
     * Send notification to a specific topic
     */
    public function sendToTopic(string $topic, string $title, string $body, array $data = []): bool
    {
        if (!$this->messaging) {
            return false;
        }

        try {
            $message = CloudMessage::withTarget('topic', $topic)
                ->withNotification(Notification::create($title, $body))
                ->withData($data);

            $this->messaging->send($message);

            return true;
        } catch (Exception $e) {
            Log::error('FCM topic send failed', [
                'topic' => $topic,
                'error' => $e->getMessage(),
            ]);
            return false;
        }
    }

    /**
     * Send broadcast notification to all users
     */
    public function broadcast(string $title, string $body, array $filters = [], array $data = []): array
    {
        $results = [
            'total_sent' => 0,
            'total_failed' => 0,
            'errors' => [],
        ];

        if (!$this->messaging) {
            $results['errors'][] = 'Firebase messaging not configured';
            return $results;
        }

        // Get target tokens based on filters
        $query = UserDevice::active();

        if (!empty($filters['country_id'])) {
            $query->whereHas('user', function ($q) use ($filters) {
                $q->where('country_id', $filters['country_id']);
            });
        }

        $tokens = $query->get();

        // Send in batches
        foreach ($tokens->chunk($this->batchSize) as $batch) {
            $batchTokens = $batch->pluck('fcm_token')->toArray();

            try {
                $message = CloudMessage::new()
                    ->withNotification(Notification::create($title, $body))
                    ->withData(array_merge($data, ['type' => 'broadcast']));

                $report = $this->messaging->sendMulticast($message, $batchTokens);

                $results['total_sent'] += $report->successes()->count();
                $results['total_failed'] += $report->failures()->count();

                $this->handleInvalidTokens($report, $batch);

            } catch (Exception $e) {
                $results['errors'][] = $e->getMessage();
            }
        }

        return $results;
    }

    /**
     * Subscribe device to topic
     */
    public function subscribeToTopic(string $token, string $topic): bool
    {
        if (!$this->messaging) {
            return false;
        }

        try {
            $this->messaging->subscribeToTopic($topic, [$token]);
            return true;
        } catch (Exception $e) {
            Log::error('FCM topic subscription failed', [
                'topic' => $topic,
                'error' => $e->getMessage(),
            ]);
            return false;
        }
    }

    /**
     * Unsubscribe device from topic
     */
    public function unsubscribeFromTopic(string $token, string $topic): bool
    {
        if (!$this->messaging) {
            return false;
        }

        try {
            $this->messaging->unsubscribeFromTopic($topic, [$token]);
            return true;
        } catch (Exception $e) {
            Log::error('FCM topic unsubscription failed', [
                'topic' => $topic,
                'error' => $e->getMessage(),
            ]);
            return false;
        }
    }

    /**
     * Get tokens for users subscribed to article's source/category/country
     */
    private function getTargetTokens(Article $article): Collection
    {
        $userIds = UserSubscription::withNotifications()
            ->where(function ($query) use ($article) {
                $query->where('source_id', $article->source_id)
                    ->orWhere('category_id', $article->category_id)
                    ->orWhere('country_id', $article->country_id);
            })
            ->pluck('user_id')
            ->unique();

        return UserDevice::active()
            ->whereIn('user_id', $userIds)
            ->get();
    }

    /**
     * Handle invalid/expired tokens
     */
    private function handleInvalidTokens($report, Collection $devices): void
    {
        $failures = $report->failures();

        foreach ($failures->getItems() as $failure) {
            $target = $failure->target();
            $error = $failure->error();

            // Check if token is invalid
            if ($error && in_array($error->getMessage(), [
                'UNREGISTERED',
                'INVALID_ARGUMENT',
                'NOT_FOUND',
            ])) {
                // Deactivate the token
                UserDevice::where('fcm_token', $target->value())->update(['is_active' => false]);
            }
        }
    }

    /**
     * Log push notifications
     */
    private function logNotifications(Collection $devices, Article $article, $report): void
    {
        $successes = $report->successes();

        foreach ($devices as $device) {
            $status = PushNotification::STATUS_PENDING;

            foreach ($successes->getItems() as $success) {
                if ($success->target()->value() === $device->fcm_token) {
                    $status = PushNotification::STATUS_SENT;
                    break;
                }
            }

            PushNotification::create([
                'user_id' => $device->user_id,
                'article_id' => $article->id,
                'title' => $article->title,
                'body' => $article->summary,
                'payload' => ['article_id' => $article->id],
                'status' => $status,
                'sent_at' => $status === PushNotification::STATUS_SENT ? now() : null,
            ]);
        }
    }
}


