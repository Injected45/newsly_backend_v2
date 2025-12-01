<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable, SoftDeletes;

    protected $fillable = [
        'name',
        'email',
        'google_id',
        'avatar',
        'country_id',
        'password',
        'email_verified_at',
        'settings',
        'language',
        'timezone',
        'last_login_at',
        'last_login_ip',
    ];

    protected $hidden = [
        'password',
        'remember_token',
        'google_id',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'last_login_at' => 'datetime',
        'settings' => 'array',
        'password' => 'hashed',
    ];

    // ==========================================
    // Relationships
    // ==========================================

    public function country(): BelongsTo
    {
        return $this->belongsTo(Country::class);
    }

    public function devices(): HasMany
    {
        return $this->hasMany(UserDevice::class);
    }

    public function subscriptions(): HasMany
    {
        return $this->hasMany(UserSubscription::class);
    }

    public function readArticles(): BelongsToMany
    {
        return $this->belongsToMany(Article::class, 'article_reads')
            ->withPivot('read_at');
    }

    public function bookmarks(): BelongsToMany
    {
        return $this->belongsToMany(Article::class, 'bookmarks')
            ->withTimestamps();
    }

    public function pushNotifications(): HasMany
    {
        return $this->hasMany(PushNotification::class);
    }

    // ==========================================
    // Scopes
    // ==========================================

    public function scopeWithGoogleId($query)
    {
        return $query->whereNotNull('google_id');
    }

    public function scopeByCountry($query, $countryId)
    {
        return $query->where('country_id', $countryId);
    }

    // ==========================================
    // Accessors & Mutators
    // ==========================================

    public function getSetting(string $key, $default = null)
    {
        return data_get($this->settings, $key, $default);
    }

    public function setSetting(string $key, $value): void
    {
        $settings = $this->settings ?? [];
        data_set($settings, $key, $value);
        $this->update(['settings' => $settings]);
    }

    // ==========================================
    // Methods
    // ==========================================

    public function isSubscribedTo(?int $sourceId = null, ?int $categoryId = null, ?int $countryId = null): bool
    {
        return $this->subscriptions()
            ->where(function ($query) use ($sourceId, $categoryId, $countryId) {
                if ($sourceId) {
                    $query->where('source_id', $sourceId);
                }
                if ($categoryId) {
                    $query->where('category_id', $categoryId);
                }
                if ($countryId) {
                    $query->where('country_id', $countryId);
                }
            })
            ->exists();
    }

    public function getActiveFcmTokens(): array
    {
        return $this->devices()
            ->where('is_active', true)
            ->pluck('fcm_token')
            ->toArray();
    }

    public function markArticleAsRead(Article $article): void
    {
        if (!$this->readArticles()->where('article_id', $article->id)->exists()) {
            $this->readArticles()->attach($article->id, ['read_at' => now()]);
        }
    }

    public function bookmarkArticle(Article $article): void
    {
        if (!$this->bookmarks()->where('article_id', $article->id)->exists()) {
            $this->bookmarks()->attach($article->id);
        }
    }

    public function removeBookmark(Article $article): void
    {
        $this->bookmarks()->detach($article->id);
    }

    public function updateLastLogin(string $ip): void
    {
        $this->update([
            'last_login_at' => now(),
            'last_login_ip' => $ip,
        ]);
    }

    public static function findByGoogleId(string $googleId): ?self
    {
        return static::where('google_id', $googleId)->first();
    }

    public static function findByEmail(string $email): ?self
    {
        return static::where('email', $email)->first();
    }
}
