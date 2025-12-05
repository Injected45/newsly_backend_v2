<?php

use App\Http\Controllers\Admin\ArticleController;
use App\Http\Controllers\Admin\AuthController;
use App\Http\Controllers\Admin\CategoryController;
use App\Http\Controllers\Admin\CountryController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\FetchLogController;
use App\Http\Controllers\Admin\NotificationController;
use App\Http\Controllers\Admin\SourceController;
use App\Http\Controllers\Admin\UserController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

// Redirect root to admin
Route::get('/', function () {
    return redirect()->route('admin.dashboard');
});

// ============================================
// Admin Routes
// ============================================

Route::prefix('admin')->name('admin.')->group(function () {
    
    // Auth routes (guest only)
    Route::middleware('guest:admin')->group(function () {
        Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
        Route::post('/login', [AuthController::class, 'login'])->name('login.submit');
    });
    
    // Protected admin routes
    Route::middleware('auth:admin')->group(function () {
        
        // Logout
        Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
        
        // Dashboard
        Route::get('/', [DashboardController::class, 'index'])->name('dashboard');
        
        // Countries
        Route::resource('countries', CountryController::class)->except(['show']);
        Route::patch('/countries/{country}/toggle-active', [CountryController::class, 'toggleActive'])->name('countries.toggle-active');
        
        // Categories
        Route::resource('categories', CategoryController::class)->except(['show']);
        Route::patch('/categories/{category}/toggle-active', [CategoryController::class, 'toggleActive'])->name('categories.toggle-active');
        
        // Sources
        Route::resource('sources', SourceController::class);
        Route::patch('/sources/{source}/toggle-active', [SourceController::class, 'toggleActive'])->name('sources.toggle-active');
        Route::post('/sources/{source}/fetch-now', [SourceController::class, 'fetchNow'])->name('sources.fetch-now');
        
        // Articles
        Route::get('/articles', [ArticleController::class, 'index'])->name('articles.index');
        Route::get('/articles/{article}', [ArticleController::class, 'show'])->name('articles.show');
        Route::patch('/articles/{article}/toggle-breaking', [ArticleController::class, 'toggleBreaking'])->name('articles.toggle-breaking');
        Route::patch('/articles/{article}/toggle-featured', [ArticleController::class, 'toggleFeatured'])->name('articles.toggle-featured');
        Route::delete('/articles/{article}', [ArticleController::class, 'destroy'])->name('articles.destroy');
        
        // Users
        Route::get('/users', [UserController::class, 'index'])->name('users.index');
        Route::get('/users/{user}', [UserController::class, 'show'])->name('users.show');
        Route::delete('/users/{user}', [UserController::class, 'destroy'])->name('users.destroy');
        
        // Fetch Logs
        Route::get('/fetch-logs', [FetchLogController::class, 'index'])->name('fetch-logs.index');
        
        // Notifications
        Route::get('/notifications', [NotificationController::class, 'index'])->name('notifications.index');
        Route::get('/notifications/create', [NotificationController::class, 'create'])->name('notifications.create');
        Route::post('/notifications/broadcast', [NotificationController::class, 'broadcast'])->name('notifications.broadcast');
    });
});



