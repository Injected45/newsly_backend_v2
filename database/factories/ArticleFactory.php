<?php

namespace Database\Factories;

use App\Models\Article;
use App\Models\Category;
use App\Models\Country;
use App\Models\Source;
use Illuminate\Database\Eloquent\Factories\Factory;

class ArticleFactory extends Factory
{
    protected $model = Article::class;

    public function definition(): array
    {
        $title = fake()->sentence(8);
        $link = fake()->url();
        $publishedAt = fake()->dateTimeBetween('-1 week', 'now');

        return [
            'source_id' => Source::factory(),
            'country_id' => Country::factory(),
            'category_id' => Category::factory(),
            'guid' => fake()->uuid(),
            'title' => $title,
            'summary' => fake()->paragraph(3),
            'content' => fake()->paragraphs(5, true),
            'link' => $link,
            'image_url' => fake()->imageUrl(800, 600),
            'published_at' => $publishedAt,
            'fetched_at' => now(),
            'is_breaking' => fake()->boolean(10),
            'is_featured' => fake()->boolean(5),
            'language' => 'ar',
            'checksum' => Article::generateChecksum($title, $link, $publishedAt->format('Y-m-d H:i:s')),
            'author' => fake()->name(),
            'tags' => fake()->words(3),
            'views_count' => fake()->numberBetween(0, 10000),
        ];
    }

    public function breaking(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_breaking' => true,
        ]);
    }

    public function featured(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_featured' => true,
        ]);
    }
}



