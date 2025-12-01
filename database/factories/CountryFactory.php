<?php

namespace Database\Factories;

use App\Models\Country;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class CountryFactory extends Factory
{
    protected $model = Country::class;

    public function definition(): array
    {
        $name = fake()->country();

        return [
            'name_ar' => $name,
            'name_en' => $name,
            'slug' => Str::slug($name) . '-' . fake()->unique()->randomNumber(4),
            'code' => strtoupper(fake()->lexify('??')),
            'flag' => fake()->emoji(),
            'is_active' => true,
            'sort_order' => fake()->numberBetween(1, 100),
        ];
    }

    public function inactive(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_active' => false,
        ]);
    }
}


