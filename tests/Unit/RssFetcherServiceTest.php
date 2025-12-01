<?php

namespace Tests\Unit;

use App\Models\Country;
use App\Models\Source;
use App\Services\RssFetcherService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class RssFetcherServiceTest extends TestCase
{
    use RefreshDatabase;

    protected Source $source;
    protected RssFetcherService $service;

    protected function setUp(): void
    {
        parent::setUp();

        $country = Country::factory()->create();
        $this->source = Source::factory()->create([
            'country_id' => $country->id,
            'rss_url' => 'https://example.com/rss',
        ]);

        $this->service = new RssFetcherService();
    }

    public function test_handles_304_not_modified(): void
    {
        Http::fake([
            'example.com/*' => Http::response('', 304),
        ]);

        $this->source->update(['http_etag' => 'test-etag']);

        $result = $this->service->fetch($this->source);

        $this->assertTrue($result['success']);
        $this->assertEquals('not_modified', $result['status']);
    }

    public function test_handles_http_error(): void
    {
        Http::fake([
            'example.com/*' => Http::response('Server Error', 500),
        ]);

        $result = $this->service->fetch($this->source);

        $this->assertFalse($result['success']);
        $this->assertEquals('error', $result['status']);
        $this->assertNotNull($result['error']);
    }

    public function test_handles_timeout(): void
    {
        Http::fake(function () {
            throw new \Illuminate\Http\Client\ConnectionException('Connection timed out');
        });

        $result = $this->service->fetch($this->source);

        $this->assertFalse($result['success']);
    }

    public function test_parses_valid_rss_feed(): void
    {
        $rssFeed = <<<XML
<?xml version="1.0" encoding="UTF-8"?>
<rss version="2.0">
    <channel>
        <title>Test Feed</title>
        <item>
            <title>Test Article</title>
            <link>https://example.com/article1</link>
            <description>Test description</description>
            <pubDate>Mon, 01 Jan 2024 12:00:00 +0000</pubDate>
        </item>
    </channel>
</rss>
XML;

        Http::fake([
            'example.com/*' => Http::response($rssFeed, 200),
        ]);

        $result = $this->service->fetch($this->source);

        $this->assertTrue($result['success']);
        $this->assertEquals(1, $result['articles_found']);
        $this->assertEquals(1, $result['articles_created']);
    }

    public function test_skips_duplicate_articles(): void
    {
        $rssFeed = <<<XML
<?xml version="1.0" encoding="UTF-8"?>
<rss version="2.0">
    <channel>
        <title>Test Feed</title>
        <item>
            <title>Test Article</title>
            <link>https://example.com/article1</link>
            <pubDate>Mon, 01 Jan 2024 12:00:00 +0000</pubDate>
        </item>
    </channel>
</rss>
XML;

        Http::fake([
            'example.com/*' => Http::response($rssFeed, 200),
        ]);

        // First fetch
        $result1 = $this->service->fetch($this->source);
        $this->assertEquals(1, $result1['articles_created']);

        // Second fetch (should skip duplicate)
        $result2 = $this->service->fetch($this->source);
        $this->assertEquals(0, $result2['articles_created']);
        $this->assertEquals(1, $result2['articles_skipped']);
    }
}


