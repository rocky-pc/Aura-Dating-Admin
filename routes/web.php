<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\AuthController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Admin\ReportController;
use App\Http\Controllers\Admin\SubscriptionController;
use App\Http\Controllers\Admin\PremiumController;
use App\Http\Controllers\Admin\AnalyticsController;

Route::get('/', function () {
    return view('welcome');
});

// Admin Login Routes
Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
Route::post('/login', [AuthController::class, 'login'])->name('login.post');
Route::get('/admin/login', [AuthController::class, 'showLoginForm'])->name('admin.login');
Route::post('/admin/login', [AuthController::class, 'login'])->name('admin.login.post');

// Admin Routes (Protected)
Route::prefix('admin')->name('admin.')->middleware('admin')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
    
    // Dashboard
    Route::get('/dashboard', [DashboardController::class, 'webIndex'])->name('dashboard');
    
    // Users
    Route::get('/users', [UserController::class, 'webIndex'])->name('users.index');
    Route::get('/users/{user}/edit-data', [UserController::class, 'editData'])->name('users.edit-data');
    Route::post('/users', [UserController::class, 'store'])->name('users.store');
    Route::put('/users/{user}', [UserController::class, 'webUpdate'])->name('users.update');
    Route::post('/users/{user}/toggle-active', [UserController::class, 'toggleActive'])->name('users.toggle-active');
    Route::post('/users/{user}/verify', [UserController::class, 'verify'])->name('users.verify');
    Route::delete('/users/{user}/web-destroy', [UserController::class, 'webDestroy'])->name('users.web-destroy');
    
    // Reports
    Route::get('/reports', [ReportController::class, 'webIndex'])->name('reports.index');
    Route::post('/reports/{report}/resolve', [ReportController::class, 'webResolve'])->name('reports.resolve');
    
    // Subscriptions
    Route::get('/subscriptions', [SubscriptionController::class, 'webIndex'])->name('subscriptions.index');
    Route::post('/subscriptions/{subscription}/cancel', [SubscriptionController::class, 'cancel'])->name('subscriptions.cancel');

    // Premium Users
    Route::get('/premium', [PremiumController::class, 'webIndex'])->name('premium.index');

    // Analytics
    Route::get('/analytics', [AnalyticsController::class, 'webIndex'])->name('analytics.index');
    
    // Wallets
    Route::get('/wallets', [\App\Http\Controllers\Admin\WalletController::class, 'webIndex'])->name('wallets.index');
    Route::get('/wallets/export', [\App\Http\Controllers\Admin\WalletController::class, 'export'])->name('wallets.export');
    Route::post('/wallets/{wallet}/add-points', [\App\Http\Controllers\Admin\WalletController::class, 'webAddPoints'])->name('wallets.add-points');
    Route::post('/wallets/{wallet}/deduct-points', [\App\Http\Controllers\Admin\WalletController::class, 'webDeductPoints'])->name('wallets.deduct-points');
    Route::post('/wallets/{wallet}/reset', [\App\Http\Controllers\Admin\WalletController::class, 'webReset'])->name('wallets.reset');

    // Wallet Payments
    Route::get('/payment-requests', [\App\Http\Controllers\Admin\WalletPaymentAdminController::class, 'index'])->name('payment-requests.index');
    Route::post('/payment-requests/{paymentRequest}/approve', [\App\Http\Controllers\Admin\WalletPaymentAdminController::class, 'approve'])->name('payment-requests.approve');
    Route::post('/payment-requests/{paymentRequest}/reject', [\App\Http\Controllers\Admin\WalletPaymentAdminController::class, 'reject'])->name('payment-requests.reject');
    Route::post('/settings/upi-id', [\App\Http\Controllers\Admin\WalletPaymentAdminController::class, 'updateSettings'])->name('settings.upi-id.update');
    
    // Favorites & Swipes
    Route::get('/favorites', [\App\Http\Controllers\Admin\FavoriteController::class, 'webIndex'])->name('favorites.index');
    Route::delete('/favorites/{swipe}', [\App\Http\Controllers\Admin\FavoriteController::class, 'destroy'])->name('favorites.destroy');

    // Swipes
    Route::get('/swipes', [\App\Http\Controllers\Admin\SwipeController::class, 'index'])->name('swipes.index');
    Route::get('/swipes/{id}', [\App\Http\Controllers\Admin\SwipeController::class, 'show'])->name('swipes.show');
    Route::delete('/swipes/{id}', [\App\Http\Controllers\Admin\SwipeController::class, 'destroy'])->name('swipes.destroy');

    // Messages
    Route::get('/messages', [\App\Http\Controllers\Admin\MessageController::class, 'index'])->name('messages.index');
    Route::get('/messages/{id}', [\App\Http\Controllers\Admin\MessageController::class, 'show'])->name('messages.show');
    Route::delete('/messages/{id}', [\App\Http\Controllers\Admin\MessageController::class, 'destroy'])->name('messages.destroy');

    // Matches & Likes
    Route::get('/matches', [\App\Http\Controllers\Admin\MatchController::class, 'index'])->name('matches.index');
    Route::get('/matches/{id}', [\App\Http\Controllers\Admin\MatchController::class, 'show'])->name('matches.show');
    Route::delete('/matches/{id}', [\App\Http\Controllers\Admin\MatchController::class, 'destroy'])->name('matches.destroy');
});
