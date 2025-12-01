<?php

namespace App\Console\Commands;

use App\Models\Article;
use App\Models\FetchLog;
use Illuminate\Console\Command;

class CleanupOldArticles extends Command
{
    protected $signature = 'news:cleanup 
        {--days=30 : Delete articles older than this many days}
        {--logs-days=7 : Delete fetch logs older than this many days}
        {--dry-run : Show what would be deleted without actually deleting}';

    protected $description = 'Clean up old articles and fetch logs';

    public function handle(): int
    {
        $articleDays = $this->option('days');
        $logsDays = $this->option('logs-days');
        $dryRun = $this->option('dry-run');

        if ($dryRun) {
            $this->warn('DRY RUN - No data will be deleted');
        }

        // Clean up old articles
        $articlesDate = now()->subDays($articleDays);
        $articlesQuery = Article::where('published_at', '<', $articlesDate);
        $articlesCount = $articlesQuery->count();

        $this->line("Articles older than {$articleDays} days: {$articlesCount}");

        if (!$dryRun && $articlesCount > 0) {
            $articlesQuery->delete();
            $this->info("✓ Deleted {$articlesCount} old articles");
        }

        // Clean up old fetch logs
        $logsDate = now()->subDays($logsDays);
        $logsQuery = FetchLog::where('created_at', '<', $logsDate);
        $logsCount = $logsQuery->count();

        $this->line("Fetch logs older than {$logsDays} days: {$logsCount}");

        if (!$dryRun && $logsCount > 0) {
            $logsQuery->delete();
            $this->info("✓ Deleted {$logsCount} old fetch logs");
        }

        return self::SUCCESS;
    }
}


