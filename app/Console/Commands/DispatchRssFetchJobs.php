<?php

namespace App\Console\Commands;

use App\Jobs\FetchRssJob;
use App\Models\Source;
use Illuminate\Console\Command;

class DispatchRssFetchJobs extends Command
{
    protected $signature = 'rss:dispatch 
        {--source= : Fetch specific source by ID}
        {--force : Force fetch even if not due}
        {--limit= : Limit number of sources to dispatch}';

    protected $description = 'Dispatch RSS fetch jobs for sources that are due';

    public function handle(): int
    {
        $sourceId = $this->option('source');
        $force = $this->option('force');
        $limit = $this->option('limit');

        if ($sourceId) {
            $source = Source::find($sourceId);
            
            if (!$source) {
                $this->error("Source #{$sourceId} not found");
                return self::FAILURE;
            }

            if (!$source->is_active) {
                $this->warn("Source #{$sourceId} is not active");
                return self::FAILURE;
            }

            FetchRssJob::dispatch($source);
            $this->info("Dispatched fetch job for source: {$source->name_en}");
            
            return self::SUCCESS;
        }

        // Get sources due for fetch
        $query = Source::active();
        
        if (!$force) {
            $query->dueForFetch();
        }

        if ($limit) {
            $query->limit($limit);
        }

        $sources = $query->get();

        if ($sources->isEmpty()) {
            $this->info('No sources due for fetch');
            return self::SUCCESS;
        }

        $maxConcurrent = config('news.rss.max_concurrent_fetches', 10);
        $dispatched = 0;

        foreach ($sources as $source) {
            if ($dispatched >= $maxConcurrent) {
                break;
            }

            FetchRssJob::dispatch($source);
            $dispatched++;
            
            $this->line("Dispatched: {$source->name_en}");
        }

        $this->info("Dispatched {$dispatched} RSS fetch jobs");
        
        return self::SUCCESS;
    }
}



