<?php

namespace App\Services;

use App\Models\Article;
use App\Models\FetchLog;
use App\Models\Source;
use Exception;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use SimplePie\SimplePie;

class RssFetcherService
{
    private int $timeout;
    private string $userAgent;
    private array $breakingKeywords;

    public function __construct()
    {
        $this->timeout = config('news.rss.request_timeout', 30);
        $this->userAgent = config('news.rss.user_agent');
        $this->breakingKeywords = config('news.breaking.keywords', []);
    }

    /**
     * Fetch RSS feed for a source
     */
    public function fetch(Source $source): array
    {
        $startTime = microtime(true);
        $result = [
            'success' => false,
            'status' => 'error',
            'articles_found' => 0,
            'articles_created' => 0,
            'articles_skipped' => 0,
            'error' => null,
        ];

        try {
            // Make HTTP request with conditional headers
            $headers = [
                'User-Agent' => $this->userAgent,
            ];

            if ($source->http_etag) {
                $headers['If-None-Match'] = $source->http_etag;
            }

            if ($source->http_last_modified) {
                $headers['If-Modified-Since'] = $source->http_last_modified;
            }

            $response = Http::withHeaders($headers)
                ->timeout($this->timeout)
                ->withoutVerifying() // Disable SSL verification for development
                ->get($source->rss_url);

            $runtimeMs = (int) ((microtime(true) - $startTime) * 1000);

            // Handle 304 Not Modified
            if ($response->status() === 304) {
                $source->markFetched();

                FetchLog::logNotModified(
                    $source->id,
                    $source->rss_url,
                    $runtimeMs
                );

                return array_merge($result, [
                    'success' => true,
                    'status' => 'not_modified',
                ]);
            }

            // Handle errors
            if (!$response->successful()) {
                throw new Exception("HTTP Error: {$response->status()}");
            }

            // Parse RSS feed
            $feed = $this->parseFeed($response->body());
            $items = $feed->get_items() ?? [];
            $result['articles_found'] = count($items);

            // Process items
            foreach ($items as $item) {
                $articleData = $this->extractArticleData($item, $source);

                if (!$articleData) {
                    $result['articles_skipped']++;
                    continue;
                }

                // Check for duplicates
                if (Article::existsByChecksum($source->id, $articleData['checksum'])) {
                    $result['articles_skipped']++;
                    continue;
                }

                // Create article
                try {
                    Article::create($articleData);
                    $result['articles_created']++;
                } catch (Exception $e) {
                    // Handle race condition (duplicate key)
                    if (Str::contains($e->getMessage(), 'Duplicate entry')) {
                        $result['articles_skipped']++;
                    } else {
                        throw $e;
                    }
                }
            }

            // Update source
            $etag = $response->header('ETag');
            $lastModified = $response->header('Last-Modified');
            $source->markFetched($etag, $lastModified);

            // Log success
            FetchLog::logSuccess(
                $source->id,
                $source->rss_url,
                $response->status(),
                $runtimeMs,
                [
                    'found' => $result['articles_found'],
                    'created' => $result['articles_created'],
                    'skipped' => $result['articles_skipped'],
                ],
                $etag,
                $lastModified,
                strlen($response->body()),
                $response->headers()
            );

            $result['success'] = true;
            $result['status'] = 'success';

        } catch (Exception $e) {
            $runtimeMs = (int) ((microtime(true) - $startTime) * 1000);
            $result['error'] = $e->getMessage();

            // Mark source as failed
            $source->markFailed();

            // Log error
            $status = Str::contains($e->getMessage(), 'timeout')
                ? FetchLog::STATUS_TIMEOUT
                : FetchLog::STATUS_ERROR;

            FetchLog::logError(
                $source->id,
                $source->rss_url,
                null,
                $runtimeMs,
                $e->getMessage(),
                $status
            );

            Log::error('RSS fetch failed', [
                'source_id' => $source->id,
                'url' => $source->rss_url,
                'error' => $e->getMessage(),
            ]);
        }

        return $result;
    }

    /**
     * Parse RSS/Atom feed
     */
    private function parseFeed(string $content): SimplePie
    {
        $feed = new SimplePie();
        $feed->set_raw_data($content);
        $feed->enable_cache(false);
        $feed->init();
        $feed->handle_content_type();

        return $feed;
    }

    /**
     * Extract article data from feed item
     */
    private function extractArticleData($item, Source $source): ?array
    {
        $title = $this->cleanText($item->get_title());
        $link = $item->get_link();
        $publishedAt = $item->get_date('Y-m-d H:i:s');

        if (empty($title) || empty($link)) {
            return null;
        }

        // Generate checksum
        $checksum = Article::generateChecksum($title, $link, $publishedAt);

        // Get content
        $content = $item->get_content();
        $description = $item->get_description();

        // Get image
        $imageUrl = $this->extractImage($item);

        // Get author
        $author = $item->get_author();
        $authorName = $author ? $author->get_name() : null;

        // Get tags/categories
        $tags = [];
        foreach ($item->get_categories() ?? [] as $category) {
            $tags[] = $category->get_label() ?? $category->get_term();
        }

        // Determine if breaking news
        $isBreaking = $source->is_breaking_source || $this->isBreakingNews($title);

        return [
            'source_id' => $source->id,
            'country_id' => $source->country_id,
            'category_id' => $source->category_id,
            'guid' => $item->get_id() ?: null,
            'title' => Str::limit($title, 497),
            'summary' => $description ? Str::limit($this->cleanText($description), 997) : null,
            'content' => $content,
            'link' => $link,
            'image_url' => $imageUrl,
            'published_at' => $publishedAt,
            'fetched_at' => now(),
            'is_breaking' => $isBreaking,
            'language' => $source->language,
            'checksum' => $checksum,
            'author' => $authorName,
            'tags' => !empty($tags) ? $tags : null,
        ];
    }

    /**
     * Extract image URL from feed item
     */
    private function extractImage($item): ?string
    {
        // Try enclosure
        $enclosure = $item->get_enclosure();
        if ($enclosure && Str::startsWith($enclosure->get_type() ?? '', 'image/')) {
            return $enclosure->get_link();
        }

        // Try media:content or media:thumbnail
        if ($enclosure && $enclosure->get_thumbnail()) {
            return $enclosure->get_thumbnail();
        }

        // Try to extract from content
        $content = $item->get_content();
        if ($content && preg_match('/<img[^>]+src=["\']([^"\']+)["\']/', $content, $matches)) {
            return $matches[1];
        }

        return null;
    }

    /**
     * Check if title contains breaking news keywords
     */
    private function isBreakingNews(string $title): bool
    {
        if (!config('news.breaking.auto_detect', true)) {
            return false;
        }

        $title = mb_strtolower($title);

        foreach ($this->breakingKeywords as $lang => $keywords) {
            foreach ($keywords as $keyword) {
                if (Str::contains($title, mb_strtolower($keyword))) {
                    return true;
                }
            }
        }

        return false;
    }

    /**
     * Clean text content
     */
    private function cleanText(?string $text): string
    {
        if (!$text) {
            return '';
        }

        // Strip HTML tags
        if (config('news.content.strip_html', true)) {
            $text = strip_tags($text, config('news.content.allowed_tags', ''));
        }

        // Decode HTML entities
        $text = html_entity_decode($text, ENT_QUOTES | ENT_HTML5, 'UTF-8');

        // Normalize whitespace
        $text = preg_replace('/\s+/', ' ', $text);

        return trim($text);
    }
}



