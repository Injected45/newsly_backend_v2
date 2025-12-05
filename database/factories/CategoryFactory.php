<?php

namespace Database\Factories;

use App\Models\Category;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class CategoryFactory extends Factory
{
    protected $model = Category::class;

    public function definition(): array
    {
        $categories = ['Politics', 'Sports', 'Technology', 'Business', 'Entertainment', 'Health', 'Science'];
        $name = fake()->randomElement($categories);

        return [
            'name_ar' => $name,
            'name_en' => $name,
            'slug' => Str::slug($name) . '-' . fake()->unique()->randomNumber(4),
            'icon' => fake()->word(),
            'color' => fake()->hexColor(),
            'is_active' => true,
            'sort_order' => fake()->numberBetween(1, 20),
        ];
    }
}



