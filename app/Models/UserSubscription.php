<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class UserSubscription extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'source_id',
        'category_id',
        'country_id',
        'notifications_enabled',
    ];

    protected $casts = [
        'notifications_enabled' => 'boolean',
    ];

    // ==========================================
    // Relationships
    // ==========================================

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function source(): BelongsTo
    {
        return $this->belongsTo(Source::class);
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function country(): BelongsTo
    {
        return $this->belongsTo(Country::class);
    }

    // ==========================================
    // Scopes
    // ==========================================

    public function scopeWithNotifications($query)
    {
        return $query->where('notifications_enabled', true);
    }

    public function scopeForSource($query, int $sourceId)
    {
        return $query->where('source_id', $sourceId);
    }

    public function scopeForCategory($query, int $categoryId)
    {
        return $query->where('category_id', $categoryId);
    }

    public function scopeForCountry($query, int $countryId)
    {
        return $query->where('country_id', $countryId);
    }

    // ==========================================
    // Methods
    // ==========================================

    public function getType(): string
    {
        if ($this->source_id) {
            return 'source';
        }
        if ($this->category_id) {
            return 'category';
        }
        if ($this->country_id) {
            return 'country';
        }
        return 'unknown';
    }

    public function getTopicName(): ?string
    {
        $topics = config('news.notifications.topics');

        if ($this->source_id) {
            return $topics['source_prefix'] . $this->source_id;
        }
        if ($this->category_id) {
            return $topics['category_prefix'] . $this->category_id;
        }
        if ($this->country_id) {
            return $topics['country_prefix'] . $this->country_id;
        }

        return null;
    }
}


