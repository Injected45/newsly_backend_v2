<?php

namespace App\Jobs;

use App\Models\Source;
use App\Services\RssFetcherService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class FetchRssJob implements ShouldQueue, ShouldBeUnique
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $tries = 3;
    public $backoff = [60, 300, 900]; // 1min, 5min, 15min
    public $timeout = 120;

    public function __construct(
        public Source $source
    ) {}

    /**
     * Unique job ID - prevents duplicate jobs for same source
     */
    public function uniqueId(): string
    {
        return 'fetch_rss_' . $this->source->id;
    }

    /**
     * How long the unique lock should be maintained
     */
    public function uniqueFor(): int
    {
        return 60; // 1 minute
    }

    public function handle(RssFetcherService $fetcher): void
    {
        Log::info('Starting RSS fetch', [
            'source_id' => $this->source->id,
            'source_name' => $this->source->name_en,
            'url' => $this->source->rss_url,
        ]);

        $result = $fetcher->fetch($this->source);

        Log::info('RSS fetch completed', [
            'source_id' => $this->source->id,
            'status' => $result['status'],
            'articles_found' => $result['articles_found'],
            'articles_created' => $result['articles_created'],
            'articles_skipped' => $result['articles_skipped'],
        ]);

        // Dispatch notifications for breaking news
        if ($result['articles_created'] > 0 && $this->source->is_breaking_source) {
            // Get newly created breaking articles
            $breakingArticles = $this->source->articles()
                ->breaking()
                ->where('fetched_at', '>=', now()->subMinutes(5))
                ->get();

            foreach ($breakingArticles as $article) {
                SendArticleNotificationJob::dispatch($article);
            }
        }
    }

    public function failed(\Throwable $exception): void
    {
        Log::error('RSS fetch job failed', [
            'source_id' => $this->source->id,
            'error' => $exception->getMessage(),
        ]);
    }
}



