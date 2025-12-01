<?php

namespace App\Jobs;

use App\Models\Article;
use App\Services\NotificationService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class SendArticleNotificationJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $tries = 3;
    public $backoff = [30, 60, 120];
    public $timeout = 300;

    public function __construct(
        public Article $article
    ) {
        $this->onQueue(config('news.notifications.queue', 'notifications'));
    }

    public function handle(NotificationService $notificationService): void
    {
        Log::info('Sending article notification', [
            'article_id' => $this->article->id,
            'title' => $this->article->title,
            'is_breaking' => $this->article->is_breaking,
        ]);

        $result = $notificationService->sendArticleNotification($this->article);

        Log::info('Article notification sent', [
            'article_id' => $this->article->id,
            'total_sent' => $result['total_sent'],
            'total_failed' => $result['total_failed'],
        ]);
    }

    public function failed(\Throwable $exception): void
    {
        Log::error('Article notification job failed', [
            'article_id' => $this->article->id,
            'error' => $exception->getMessage(),
        ]);
    }
}


