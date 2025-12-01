<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Article extends Model
{
    use HasFactory;

    protected $fillable = [
        'source_id',
        'country_id',
        'category_id',
        'guid',
        'title',
        'summary',
        'content',
        'link',
        'image_url',
        'published_at',
        'fetched_at',
        'is_breaking',
        'is_featured',
        'language',
        'checksum',
        'author',
        'tags',
        'views_count',
    ];

    protected $casts = [
        'is_breaking' => 'boolean',
        'is_featured' => 'boolean',
        'published_at' => 'datetime',
        'fetched_at' => 'datetime',
        'tags' => 'array',
        'views_count' => 'integer',
    ];

    // ==========================================
    // Relationships
    // ==========================================

    public function source(): BelongsTo
    {
        return $this->belongsTo(Source::class);
    }

    public function country(): BelongsTo
    {
        return $this->belongsTo(Country::class);
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function readers(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'article_reads')
            ->withPivot('read_at');
    }

    public function bookmarkedBy(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'bookmarks')
            ->withTimestamps();
    }

    public function notifications(): HasMany
    {
        return $this->hasMany(PushNotification::class);
    }

    // ==========================================
    // Scopes
    // ==========================================

    public function scopeBreaking($query)
    {
        return $query->where('is_breaking', true);
    }

    public function scopeFeatured($query)
    {
        return $query->where('is_featured', true);
    }

    public function scopeByCountry($query, $countryId)
    {
        return $query->where('country_id', $countryId);
    }

    public function scopeByCategory($query, $categoryId)
    {
        return $query->where('category_id', $categoryId);
    }

    public function scopeBySource($query, $sourceId)
    {
        return $query->where('source_id', $sourceId);
    }

    public function scopePublishedAfter($query, $timestamp)
    {
        return $query->where('published_at', '>', $timestamp);
    }

    public function scopeLatest($query)
    {
        return $query->orderBy('published_at', 'desc');
    }

    public function scopeSearch($query, string $term)
    {
        return $query->whereRaw(
            'MATCH(title, summary) AGAINST(? IN NATURAL LANGUAGE MODE)',
            [$term]
        );
    }

    // ==========================================
    // Static Methods
    // ==========================================

    public static function generateChecksum(string $title, string $link, ?string $publishedAt): string
    {
        return hash('sha256', $title . $link . ($publishedAt ?? ''));
    }

    public static function existsByChecksum(int $sourceId, string $checksum): bool
    {
        return static::where('source_id', $sourceId)
            ->where('checksum', $checksum)
            ->exists();
    }

    // ==========================================
    // Methods
    // ==========================================

    public function incrementViews(): void
    {
        $this->increment('views_count');
    }

    public function markAsBreaking(bool $isBreaking = true): void
    {
        $this->update(['is_breaking' => $isBreaking]);
    }

    public function markAsFeatured(bool $isFeatured = true): void
    {
        $this->update(['is_featured' => $isFeatured]);
    }

    public function isReadBy(User $user): bool
    {
        return $this->readers()->where('user_id', $user->id)->exists();
    }

    public function isBookmarkedBy(User $user): bool
    {
        return $this->bookmarkedBy()->where('user_id', $user->id)->exists();
    }
}


