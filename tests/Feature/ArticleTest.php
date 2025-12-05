<?php

namespace Tests\Feature;

use App\Models\Article;
use App\Models\Category;
use App\Models\Country;
use App\Models\Source;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ArticleTest extends TestCase
{
    use RefreshDatabase;

    protected Country $country;
    protected Category $category;
    protected Source $source;

    protected function setUp(): void
    {
        parent::setUp();

        $this->country = Country::factory()->create();
        $this->category = Category::factory()->create();
        $this->source = Source::factory()->create([
            'country_id' => $this->country->id,
            'category_id' => $this->category->id,
        ]);
    }

    public function test_can_list_articles(): void
    {
        Article::factory()->count(5)->create([
            'source_id' => $this->source->id,
            'country_id' => $this->country->id,
        ]);

        $response = $this->getJson('/api/articles');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'success',
                'data' => [
                    'items',
                    'meta' => ['current_page', 'total', 'per_page'],
                ],
            ])
            ->assertJsonCount(5, 'data.items');
    }

    public function test_can_filter_articles_by_country(): void
    {
        Article::factory()->count(3)->create([
            'source_id' => $this->source->id,
            'country_id' => $this->country->id,
        ]);

        $otherCountry = Country::factory()->create();
        $otherSource = Source::factory()->create(['country_id' => $otherCountry->id]);
        Article::factory()->count(2)->create([
            'source_id' => $otherSource->id,
            'country_id' => $otherCountry->id,
        ]);

        $response = $this->getJson('/api/articles?country_id=' . $this->country->id);

        $response->assertStatus(200)
            ->assertJsonCount(3, 'data.items');
    }

    public function test_can_get_breaking_news(): void
    {
        Article::factory()->count(3)->create([
            'source_id' => $this->source->id,
            'country_id' => $this->country->id,
            'is_breaking' => true,
        ]);

        Article::factory()->count(2)->create([
            'source_id' => $this->source->id,
            'country_id' => $this->country->id,
            'is_breaking' => false,
        ]);

        $response = $this->getJson('/api/articles/breaking');

        $response->assertStatus(200)
            ->assertJsonCount(3, 'data');
    }

    public function test_can_get_latest_articles(): void
    {
        Article::factory()->count(30)->create([
            'source_id' => $this->source->id,
            'country_id' => $this->country->id,
        ]);

        $response = $this->getJson('/api/articles/latest?limit=10');

        $response->assertStatus(200)
            ->assertJsonCount(10, 'data');
    }

    public function test_can_get_single_article(): void
    {
        $article = Article::factory()->create([
            'source_id' => $this->source->id,
            'country_id' => $this->country->id,
        ]);

        $response = $this->getJson('/api/articles/' . $article->id);

        $response->assertStatus(200)
            ->assertJsonPath('data.id', $article->id)
            ->assertJsonPath('data.title', $article->title);
    }

    public function test_authenticated_user_can_mark_article_as_read(): void
    {
        $user = User::factory()->create();
        $article = Article::factory()->create([
            'source_id' => $this->source->id,
            'country_id' => $this->country->id,
        ]);

        $response = $this->actingAs($user, 'sanctum')
            ->postJson('/api/articles/mark-read', [
                'article_id' => $article->id,
            ]);

        $response->assertStatus(200);

        $this->assertDatabaseHas('article_reads', [
            'user_id' => $user->id,
            'article_id' => $article->id,
        ]);
    }

    public function test_can_search_articles(): void
    {
        Article::factory()->create([
            'source_id' => $this->source->id,
            'country_id' => $this->country->id,
            'title' => 'Breaking news about technology',
        ]);

        Article::factory()->create([
            'source_id' => $this->source->id,
            'country_id' => $this->country->id,
            'title' => 'Sports update today',
        ]);

        $response = $this->getJson('/api/articles?search=technology');

        $response->assertStatus(200);
    }
}



