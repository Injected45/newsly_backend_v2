<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class UserDevice extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'platform',
        'fcm_token',
        'device_id',
        'device_name',
        'device_model',
        'os_version',
        'app_version',
        'is_active',
        'last_active_at',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'last_active_at' => 'datetime',
    ];

    // ==========================================
    // Relationships
    // ==========================================

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    // ==========================================
    // Scopes
    // ==========================================

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeByPlatform($query, string $platform)
    {
        return $query->where('platform', $platform);
    }

    public function scopeAndroid($query)
    {
        return $query->where('platform', 'android');
    }

    public function scopeIos($query)
    {
        return $query->where('platform', 'ios');
    }

    // ==========================================
    // Methods
    // ==========================================

    public function markActive(): void
    {
        $this->update([
            'is_active' => true,
            'last_active_at' => now(),
        ]);
    }

    public function markInactive(): void
    {
        $this->update(['is_active' => false]);
    }

    public static function updateOrCreateToken(
        int $userId,
        string $platform,
        string $fcmToken,
        array $deviceInfo = []
    ): self {
        return static::updateOrCreate(
            [
                'user_id' => $userId,
                'fcm_token' => $fcmToken,
            ],
            array_merge([
                'platform' => $platform,
                'is_active' => true,
                'last_active_at' => now(),
            ], $deviceInfo)
        );
    }
}



