<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Country;
use App\Models\Source;
use App\Models\User;
use App\Models\UserSubscription;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SubscriptionTest extends TestCase
{
    use RefreshDatabase;

    protected User $user;
    protected Country $country;
    protected Category $category;
    protected Source $source;

    protected function setUp(): void
    {
        parent::setUp();

        $this->user = User::factory()->create();
        $this->country = Country::factory()->create();
        $this->category = Category::factory()->create();
        $this->source = Source::factory()->create([
            'country_id' => $this->country->id,
            'category_id' => $this->category->id,
        ]);
    }

    public function test_can_list_subscriptions(): void
    {
        UserSubscription::factory()->count(3)->create([
            'user_id' => $this->user->id,
            'source_id' => $this->source->id,
        ]);

        $response = $this->actingAs($this->user, 'sanctum')
            ->getJson('/api/subscriptions');

        $response->assertStatus(200)
            ->assertJsonCount(3, 'data');
    }

    public function test_can_subscribe_to_source(): void
    {
        $response = $this->actingAs($this->user, 'sanctum')
            ->postJson('/api/subscriptions', [
                'source_id' => $this->source->id,
            ]);

        $response->assertStatus(201);

        $this->assertDatabaseHas('user_subscriptions', [
            'user_id' => $this->user->id,
            'source_id' => $this->source->id,
        ]);
    }

    public function test_can_subscribe_to_category(): void
    {
        $response = $this->actingAs($this->user, 'sanctum')
            ->postJson('/api/subscriptions', [
                'category_id' => $this->category->id,
            ]);

        $response->assertStatus(201);

        $this->assertDatabaseHas('user_subscriptions', [
            'user_id' => $this->user->id,
            'category_id' => $this->category->id,
        ]);
    }

    public function test_can_subscribe_to_country(): void
    {
        $response = $this->actingAs($this->user, 'sanctum')
            ->postJson('/api/subscriptions', [
                'country_id' => $this->country->id,
            ]);

        $response->assertStatus(201);

        $this->assertDatabaseHas('user_subscriptions', [
            'user_id' => $this->user->id,
            'country_id' => $this->country->id,
        ]);
    }

    public function test_cannot_duplicate_subscription(): void
    {
        UserSubscription::factory()->create([
            'user_id' => $this->user->id,
            'source_id' => $this->source->id,
        ]);

        $response = $this->actingAs($this->user, 'sanctum')
            ->postJson('/api/subscriptions', [
                'source_id' => $this->source->id,
            ]);

        $response->assertStatus(409);
    }

    public function test_can_unsubscribe(): void
    {
        $subscription = UserSubscription::factory()->create([
            'user_id' => $this->user->id,
            'source_id' => $this->source->id,
        ]);

        $response = $this->actingAs($this->user, 'sanctum')
            ->deleteJson('/api/subscriptions/' . $subscription->id);

        $response->assertStatus(200);

        $this->assertDatabaseMissing('user_subscriptions', [
            'id' => $subscription->id,
        ]);
    }

    public function test_cannot_unsubscribe_others_subscription(): void
    {
        $otherUser = User::factory()->create();
        $subscription = UserSubscription::factory()->create([
            'user_id' => $otherUser->id,
            'source_id' => $this->source->id,
        ]);

        $response = $this->actingAs($this->user, 'sanctum')
            ->deleteJson('/api/subscriptions/' . $subscription->id);

        $response->assertStatus(403);
    }

    public function test_subscription_requires_at_least_one_target(): void
    {
        $response = $this->actingAs($this->user, 'sanctum')
            ->postJson('/api/subscriptions', []);

        $response->assertStatus(422);
    }
}



