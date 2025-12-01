<?php

namespace Tests\Unit;

use App\Models\Article;
use PHPUnit\Framework\TestCase;

class ArticleChecksumTest extends TestCase
{
    public function test_generates_consistent_checksum(): void
    {
        $title = 'Test Article Title';
        $link = 'https://example.com/article';
        $publishedAt = '2024-01-01 12:00:00';

        $checksum1 = Article::generateChecksum($title, $link, $publishedAt);
        $checksum2 = Article::generateChecksum($title, $link, $publishedAt);

        $this->assertEquals($checksum1, $checksum2);
    }

    public function test_different_inputs_produce_different_checksums(): void
    {
        $checksum1 = Article::generateChecksum('Title 1', 'https://example.com/1', '2024-01-01');
        $checksum2 = Article::generateChecksum('Title 2', 'https://example.com/2', '2024-01-01');

        $this->assertNotEquals($checksum1, $checksum2);
    }

    public function test_checksum_is_64_characters(): void
    {
        $checksum = Article::generateChecksum('Test', 'https://example.com', null);

        $this->assertEquals(64, strlen($checksum));
    }

    public function test_handles_null_published_at(): void
    {
        $checksum = Article::generateChecksum('Test Title', 'https://example.com', null);

        $this->assertNotEmpty($checksum);
        $this->assertEquals(64, strlen($checksum));
    }
}


