<?php

namespace Database\Factories;

use App\Models\Category;
use App\Models\Country;
use App\Models\Source;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class SourceFactory extends Factory
{
    protected $model = Source::class;

    public function definition(): array
    {
        $name = fake()->company();

        return [
            'country_id' => Country::factory(),
            'category_id' => Category::factory(),
            'name_ar' => $name,
            'name_en' => $name,
            'slug' => Str::slug($name) . '-' . fake()->unique()->randomNumber(4),
            'rss_url' => fake()->url() . '/rss',
            'website_url' => fake()->url(),
            'logo' => fake()->imageUrl(100, 100),
            'is_active' => true,
            'is_breaking_source' => fake()->boolean(20),
            'fetch_interval_seconds' => fake()->randomElement([300, 600, 900, 1800]),
            'language' => fake()->randomElement(['ar', 'en']),
        ];
    }

    public function inactive(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_active' => false,
        ]);
    }

    public function breaking(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_breaking_source' => true,
        ]);
    }
}


