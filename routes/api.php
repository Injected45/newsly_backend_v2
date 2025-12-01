<?php

use App\Http\Controllers\Api\ArticleController;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\BookmarkController;
use App\Http\Controllers\Api\CategoryController;
use App\Http\Controllers\Api\CountryController;
use App\Http\Controllers\Api\NotificationController;
use App\Http\Controllers\Api\SourceController;
use App\Http\Controllers\Api\SubscriptionController;
use App\Http\Controllers\Api\UserController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
*/

// Health check
Route::get('/health', function () {
    return response()->json([
        'status' => 'ok',
        'timestamp' => now()->toISOString(),
    ]);
});

// ============================================
// Public Routes (no authentication required)
// ============================================

// Auth routes
Route::prefix('auth')->group(function () {
    Route::post('/google', [AuthController::class, 'google'])->middleware('throttle:10,1');
});

// Countries
Route::get('/countries', [CountryController::class, 'index']);
Route::get('/countries/{country}', [CountryController::class, 'show']);

// Categories
Route::get('/categories', [CategoryController::class, 'index']);
Route::get('/categories/{category}', [CategoryController::class, 'show']);

// Sources
Route::get('/sources', [SourceController::class, 'index']);
Route::get('/sources/{source}', [SourceController::class, 'show']);

// Articles (public)
Route::get('/articles', [ArticleController::class, 'index']);
Route::get('/articles/latest', [ArticleController::class, 'latest']);
Route::get('/articles/breaking', [ArticleController::class, 'breaking']);
Route::get('/articles/{article}', [ArticleController::class, 'show'])->name('articles.show');


// ============================================
// Protected Routes (authentication required)
// ============================================

Route::middleware('auth:sanctum')->group(function () {
    
    // Auth
    Route::prefix('auth')->group(function () {
        Route::get('/me', [AuthController::class, 'me']);
        Route::post('/logout', [AuthController::class, 'logout']);
        Route::post('/logout-all', [AuthController::class, 'logoutAll']);
    });
    
    // User profile
    Route::prefix('user')->group(function () {
        Route::put('/', [UserController::class, 'update']);
        Route::post('/device', [UserController::class, 'registerDevice']);
        Route::delete('/device', [UserController::class, 'unregisterDevice']);
        Route::get('/notifications/settings', [UserController::class, 'notificationSettings']);
        Route::post('/notifications/settings', [UserController::class, 'updateNotificationSettings']);
    });
    
    // Articles (authenticated actions)
    Route::post('/articles/mark-read', [ArticleController::class, 'markRead']);
    Route::get('/articles/history', [ArticleController::class, 'readHistory']);
    
    // Bookmarks
    Route::prefix('bookmarks')->group(function () {
        Route::get('/', [BookmarkController::class, 'index']);
        Route::post('/', [BookmarkController::class, 'store']);
        Route::delete('/{article}', [BookmarkController::class, 'destroy']);
    });
    
    // Subscriptions
    Route::prefix('subscriptions')->group(function () {
        Route::get('/', [SubscriptionController::class, 'index']);
        Route::post('/', [SubscriptionController::class, 'store']);
        Route::put('/{subscription}', [SubscriptionController::class, 'update']);
        Route::delete('/{subscription}', [SubscriptionController::class, 'destroy']);
        Route::post('/sync', [SubscriptionController::class, 'sync']);
    });
    
    // Notifications
    Route::prefix('notifications')->group(function () {
        Route::get('/', [NotificationController::class, 'index']);
        Route::get('/unread-count', [NotificationController::class, 'unreadCount']);
        Route::post('/{notification}/read', [NotificationController::class, 'markRead']);
        Route::post('/mark-all-read', [NotificationController::class, 'markAllRead']);
    });
});


