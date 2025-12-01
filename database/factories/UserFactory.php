<?php

namespace Database\Factories;

use App\Models\Country;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class UserFactory extends Factory
{
    protected static ?string $password;

    public function definition(): array
    {
        return [
            'name' => fake()->name(),
            'email' => fake()->unique()->safeEmail(),
            'google_id' => fake()->unique()->numerify('####################'),
            'avatar' => fake()->imageUrl(200, 200, 'people'),
            'country_id' => Country::factory(),
            'password' => static::$password ??= Hash::make('password'),
            'email_verified_at' => now(),
            'settings' => [
                'notifications_enabled' => true,
                'breaking_only' => false,
            ],
            'language' => 'ar',
            'timezone' => 'UTC',
            'remember_token' => Str::random(10),
        ];
    }

    public function unverified(): static
    {
        return $this->state(fn (array $attributes) => [
            'email_verified_at' => null,
        ]);
    }

    public function googleOnly(): static
    {
        return $this->state(fn (array $attributes) => [
            'password' => null,
        ]);
    }
}


