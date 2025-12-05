<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class FetchLog extends Model
{
    use HasFactory;

    public $timestamps = false;

    protected $fillable = [
        'source_id',
        'request_url',
        'http_status',
        'runtime_ms',
        'etag_received',
        'last_modified_received',
        'response_size_bytes',
        'articles_found',
        'articles_created',
        'articles_skipped',
        'status',
        'error_message',
        'response_headers',
        'created_at',
    ];

    protected $casts = [
        'http_status' => 'integer',
        'runtime_ms' => 'integer',
        'response_size_bytes' => 'integer',
        'articles_found' => 'integer',
        'articles_created' => 'integer',
        'articles_skipped' => 'integer',
        'response_headers' => 'array',
        'created_at' => 'datetime',
    ];

    // ==========================================
    // Constants
    // ==========================================

    public const STATUS_SUCCESS = 'success';
    public const STATUS_NOT_MODIFIED = 'not_modified';
    public const STATUS_ERROR = 'error';
    public const STATUS_TIMEOUT = 'timeout';

    // ==========================================
    // Relationships
    // ==========================================

    public function source(): BelongsTo
    {
        return $this->belongsTo(Source::class);
    }

    // ==========================================
    // Scopes
    // ==========================================

    public function scopeSuccessful($query)
    {
        return $query->whereIn('status', [self::STATUS_SUCCESS, self::STATUS_NOT_MODIFIED]);
    }

    public function scopeFailed($query)
    {
        return $query->whereIn('status', [self::STATUS_ERROR, self::STATUS_TIMEOUT]);
    }

    public function scopeForSource($query, int $sourceId)
    {
        return $query->where('source_id', $sourceId);
    }

    public function scopeRecent($query, int $hours = 24)
    {
        return $query->where('created_at', '>=', now()->subHours($hours));
    }

    // ==========================================
    // Static Methods
    // ==========================================

    public static function logSuccess(
        int $sourceId,
        string $requestUrl,
        int $httpStatus,
        int $runtimeMs,
        array $stats,
        ?string $etag = null,
        ?string $lastModified = null,
        ?int $responseSize = null,
        ?array $headers = null
    ): self {
        return static::create([
            'source_id' => $sourceId,
            'request_url' => $requestUrl,
            'http_status' => $httpStatus,
            'runtime_ms' => $runtimeMs,
            'etag_received' => $etag,
            'last_modified_received' => $lastModified,
            'response_size_bytes' => $responseSize,
            'articles_found' => $stats['found'] ?? 0,
            'articles_created' => $stats['created'] ?? 0,
            'articles_skipped' => $stats['skipped'] ?? 0,
            'status' => self::STATUS_SUCCESS,
            'response_headers' => $headers,
            'created_at' => now(),
        ]);
    }

    public static function logNotModified(
        int $sourceId,
        string $requestUrl,
        int $runtimeMs
    ): self {
        return static::create([
            'source_id' => $sourceId,
            'request_url' => $requestUrl,
            'http_status' => 304,
            'runtime_ms' => $runtimeMs,
            'status' => self::STATUS_NOT_MODIFIED,
            'created_at' => now(),
        ]);
    }

    public static function logError(
        int $sourceId,
        string $requestUrl,
        ?int $httpStatus,
        int $runtimeMs,
        string $errorMessage,
        string $status = self::STATUS_ERROR
    ): self {
        return static::create([
            'source_id' => $sourceId,
            'request_url' => $requestUrl,
            'http_status' => $httpStatus,
            'runtime_ms' => $runtimeMs,
            'status' => $status,
            'error_message' => $errorMessage,
            'created_at' => now(),
        ]);
    }
}



