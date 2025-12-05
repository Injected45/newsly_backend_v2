<?php

namespace Database\Factories;

use App\Models\Source;
use App\Models\User;
use App\Models\UserSubscription;
use Illuminate\Database\Eloquent\Factories\Factory;

class UserSubscriptionFactory extends Factory
{
    protected $model = UserSubscription::class;

    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'source_id' => Source::factory(),
            'category_id' => null,
            'country_id' => null,
            'notifications_enabled' => true,
        ];
    }

    public function toCategory(): static
    {
        return $this->state(fn (array $attributes) => [
            'source_id' => null,
            'category_id' => \App\Models\Category::factory(),
        ]);
    }

    public function toCountry(): static
    {
        return $this->state(fn (array $attributes) => [
            'source_id' => null,
            'country_id' => \App\Models\Country::factory(),
        ]);
    }
}



