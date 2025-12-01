<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Country extends Model
{
    use HasFactory;

    protected $fillable = [
        'name_ar',
        'name_en',
        'slug',
        'code',
        'flag',
        'is_active',
        'sort_order',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'sort_order' => 'integer',
    ];

    // ==========================================
    // Relationships
    // ==========================================

    public function sources(): HasMany
    {
        return $this->hasMany(Source::class);
    }

    public function articles(): HasMany
    {
        return $this->hasMany(Article::class);
    }

    public function users(): HasMany
    {
        return $this->hasMany(User::class);
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

    public function scopeOrdered($query)
    {
        return $query->orderBy('sort_order')->orderBy('name_en');
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

    public function getSourcesCount(): int
    {
        return $this->sources()->active()->count();
    }

    public function getArticlesCount(): int
    {
        return $this->articles()->count();
    }
}


