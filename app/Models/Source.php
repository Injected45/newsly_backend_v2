<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Source extends Model
{
    use HasFactory;

    protected $fillable = [
        'country_id',
        'category_id',
        'name_ar',
        'name_en',
        'slug',
        'rss_url',
        'website_url',
        'logo',
        'is_active',
        'is_breaking_source',
        'fetch_interval_seconds',
        'last_fetched_at',
        'http_etag',
        'http_last_modified',
        'consecutive_failures',
        'next_fetch_at',
        'language',
        'settings',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'is_breaking_source' => 'boolean',
        'fetch_interval_seconds' => 'integer',
        'consecutive_failures' => 'integer',
        'last_fetched_at' => 'datetime',
        'next_fetch_at' => 'datetime',
        'settings' => 'array',
    ];

    // ==========================================
    // Relationships
    // ==========================================

    public function country(): BelongsTo
    {
        return $this->belongsTo(Country::class);
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function articles(): HasMany
    {
        return $this->hasMany(Article::class);
    }

    public function fetchLogs(): HasMany
    {
        return $this->hasMany(FetchLog::class);
    }

    public function subscriptions(): HasMany
    {
        return $this->hasMany(UserSubscription::class);
    }

    // ==========================================
    // Scopes
    // ==========================================

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeBreaking($query)
    {
        return $query->where('is_breaking_source', true);
    }

    public function scopeDueForFetch($query)
    {
        return $query->active()
            ->where(function ($q) {
                $q->whereNull('next_fetch_at')
                    ->orWhere('next_fetch_at', '<=', now());
            });
    }

    public function scopeByCountry($query, $countryId)
    {
        return $query->where('country_id', $countryId);
    }

    public function scopeByCategory($query, $categoryId)
    {
        return $query->where('category_id', $categoryId);
    }

    // ==========================================
    // Accessors
    // ==========================================

    public function getNameAttribute(): string
    {
        return app()->getLocale() === 'ar' ? $this->name_ar : $this->name_en;
    }

    // ==========================================
    // Methods
    // ==========================================

    public function markFetched(?string $etag = null, ?string $lastModified = null): void
    {
        $this->update([
            'last_fetched_at' => now(),
            'next_fetch_at' => now()->addSeconds($this->fetch_interval_seconds),
            'http_etag' => $etag ?? $this->http_etag,
            'http_last_modified' => $lastModified ?? $this->http_last_modified,
            'consecutive_failures' => 0,
        ]);
    }

    public function markFailed(): void
    {
        $failures = $this->consecutive_failures + 1;
        $backoffSeconds = min(3600, $this->fetch_interval_seconds * pow(2, $failures));

        $this->update([
            'consecutive_failures' => $failures,
            'next_fetch_at' => now()->addSeconds($backoffSeconds),
        ]);
    }

    public function getTopicName(): string
    {
        return config('news.notifications.topics.source_prefix') . $this->id;
    }

    public function getArticlesCount(): int
    {
        return $this->articles()->count();
    }

    public function getSubscribersCount(): int
    {
        return $this->subscriptions()->count();
    }
}



