<?php

namespace App\Console\Commands;

use App\Models\Source;
use App\Services\RssFetcherService;
use Illuminate\Console\Command;

class FetchRssNow extends Command
{
    protected $signature = 'rss:fetch 
        {source : Source ID to fetch}
        {--sync : Run synchronously instead of dispatching job}';

    protected $description = 'Fetch RSS feed for a specific source immediately';

    public function handle(RssFetcherService $fetcher): int
    {
        $sourceId = $this->argument('source');
        $source = Source::find($sourceId);

        if (!$source) {
            $this->error("Source #{$sourceId} not found");
            return self::FAILURE;
        }

        $this->info("Fetching RSS for: {$source->name_en}");
        $this->line("URL: {$source->rss_url}");

        $startTime = microtime(true);
        $result = $fetcher->fetch($source);
        $duration = round((microtime(true) - $startTime) * 1000);

        if ($result['success']) {
            $this->info("✓ Fetch completed in {$duration}ms");
            $this->table(
                ['Metric', 'Value'],
                [
                    ['Status', $result['status']],
                    ['Articles Found', $result['articles_found']],
                    ['Articles Created', $result['articles_created']],
                    ['Articles Skipped', $result['articles_skipped']],
                ]
            );
            return self::SUCCESS;
        } else {
            $this->error("✗ Fetch failed: {$result['error']}");
            return self::FAILURE;
        }
    }
}



