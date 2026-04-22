<?php

use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Admin\ReportController;
use App\Http\Controllers\Admin\SubscriptionController;
use App\Http\Controllers\Admin\WalletController as AdminWalletController;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\UserController as ApiUserController;
use App\Http\Controllers\Api\MatchController;
use App\Http\Controllers\Api\ChatController;
use App\Http\Controllers\Api\WalletController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
*/

// Public API Routes
Route::prefix('auth')->group(function () {
    Route::post('/register', [AuthController::class, 'register']);
    Route::post('/login', [AuthController::class, 'login']);
    Route::post('/otp/send', [AuthController::class, 'sendOtp']);
    Route::post('/otp/verify', [AuthController::class, 'verifyOtp']);
    Route::post('/forgot-password', [AuthController::class, 'forgotPassword']);
});

// Protected API Routes
Route::middleware('auth:sanctum')->group(function () {
    // Auth
    Route::prefix('auth')->group(function () {
        Route::get('/me', [AuthController::class, 'me']);
        Route::post('/logout', [AuthController::class, 'logout']);
        Route::put('/profile', [AuthController::class, 'updateProfile']);
        Route::post('/change-password', [AuthController::class, 'changePassword']);
    });

    // Users
    Route::prefix('users')->group(function () {
        Route::get('/discovery', [ApiUserController::class, 'discovery']);
        Route::get('/nearby', [ApiUserController::class, 'nearby']);
        Route::get('/{id}', [ApiUserController::class, 'show']);
        Route::get('/{id}/connection-status', [ApiUserController::class, 'getConnectionStatus']);
        Route::post('/{id}/connection-request', [ApiUserController::class, 'sendConnectionRequest']);
        Route::delete('/{id}/connection', [ApiUserController::class, 'removeConnection']);
        Route::put('/{id}/like', [ApiUserController::class, 'like']);
        Route::put('/{id}/dislike', [ApiUserController::class, 'dislike']);
        Route::put('/{id}/super-like', [ApiUserController::class, 'superLike']);
        Route::put('/{id}/report', [ApiUserController::class, 'report']);
        Route::put('/{id}/block', [ApiUserController::class, 'block']);
        Route::put('/profile', [ApiUserController::class, 'updateProfile']);
        Route::post('/profile/images', [ApiUserController::class, 'uploadImage']);
        Route::delete('/profile/images/{id}', [ApiUserController::class, 'deleteImage']);
        Route::get('/profile/hobbies', [ApiUserController::class, 'getHobbies']);
        Route::post('/profile/hobbies', [ApiUserController::class, 'updateHobbies']);
        Route::get('/me/discovery-settings', [ApiUserController::class, 'getDiscoverySettings']);
        Route::put('/me/discovery-settings', [ApiUserController::class, 'updateDiscoverySettings']);
    });

    // Connection Requests
    Route::prefix('connection-requests')->group(function () {
        Route::get('/', [ApiUserController::class, 'getConnectionRequests']);
        Route::get('/pending', [ApiUserController::class, 'getPendingConnectionRequests']);
        Route::put('/{id}/accept', [ApiUserController::class, 'acceptConnectionRequest']);
        Route::put('/{id}/decline', [ApiUserController::class, 'declineConnectionRequest']);
    });

    // Matches
    Route::prefix('matches')->group(function () {
        Route::get('/', [MatchController::class, 'index']);
        Route::get('/{id}', [MatchController::class, 'show']);
        Route::put('/{id}/unmatch', [MatchController::class, 'unmatch']);
    });

    // Likes
    Route::post('/like', [MatchController::class, 'like']);
    Route::post('/like_action', [MatchController::class, 'likeAction']);
    Route::get('/matches', [MatchController::class, 'getMatches']);

    // Chat
    Route::prefix('chat')->group(function () {
        Route::get('/conversations', [ChatController::class, 'conversations']);
        Route::post('/conversations', [ChatController::class, 'createConversation']);
        Route::get('/conversations/{id}/messages', [ChatController::class, 'messages']);
        Route::post('/conversations/{id}/messages', [ChatController::class, 'sendMessage']);
        Route::put('/messages/{id}/read', [ChatController::class, 'markAsRead']);
    });

    // Subscriptions
    Route::prefix('subscriptions')->group(function () {
        Route::get('/plans', [SubscriptionController::class, 'plans']);
        Route::post('/subscribe', [SubscriptionController::class, 'subscribe']);
        Route::post('/cancel', [SubscriptionController::class, 'cancel']);
    });

    // Notifications
    Route::get('/notifications', [ApiUserController::class, 'notifications']);
    Route::put('/notifications/{id}/read', [ApiUserController::class, 'markNotificationRead']);
    Route::put('/notifications/read-all', [ApiUserController::class, 'markAllNotificationsRead']);

    // Wallet
    Route::get('/wallet', [WalletController::class, 'show']);
    Route::get('/wallet/settings', [WalletController::class, 'settings']);
    Route::post('/wallet/add-points', [WalletController::class, 'addPoints']);
    Route::post('/wallet/deduct-points', [WalletController::class, 'deductPoints']);
    Route::get('/wallet/transactions', [WalletController::class, 'transactions']);

    // Wallet Payments
    Route::get('/wallet/upi-id', [\App\Http\Controllers\Api\WalletPaymentController::class, 'getUpiId']);
    Route::post('/wallet/payment-request', [\App\Http\Controllers\Api\WalletPaymentController::class, 'createRequest']);
    Route::get('/wallet/payment-requests', [\App\Http\Controllers\Api\WalletPaymentController::class, 'myRequests']);
});

// Admin Routes
Route::prefix('admin')->middleware(['auth:sanctum', 'admin'])->group(function () {
    // Dashboard
    Route::get('/dashboard', [DashboardController::class, 'index']);
    Route::get('/health', [DashboardController::class, 'health']);

    // Users
    Route::apiResource('users', UserController::class);
    Route::post('/users/{user}/verify', [UserController::class, 'verify']);
    Route::post('/users/{user}/toggle-active', [UserController::class, 'toggleActive']);
    Route::post('/users/{user}/block', [UserController::class, 'block']);
    Route::post('/users/{user}/unblock', [UserController::class, 'unblock']);
    Route::get('/users-stats', [UserController::class, 'stats']);
    


    // Reports
    Route::apiResource('reports', ReportController::class);
    Route::post('/reports/{report}/resolve', [ReportController::class, 'resolve']);
    Route::get('/reports-stats', [ReportController::class, 'stats']);
    Route::get('/reports-reasons', [ReportController::class, 'reasons']);

    // Subscriptions
    Route::apiResource('subscriptions', SubscriptionController::class);
    Route::post('/subscriptions/{subscription}/cancel', [SubscriptionController::class, 'cancel']);
    Route::post('/subscriptions/{subscription}/extend', [SubscriptionController::class, 'extend']);
    Route::get('/subscriptions-stats', [SubscriptionController::class, 'stats']);
    Route::get('/subscriptions-plans', [SubscriptionController::class, 'plans']);

    // Wallets
    Route::get('/wallets', [AdminWalletController::class, 'index']);
    Route::get('/wallets/{wallet}', [AdminWalletController::class, 'show']);
    Route::post('/wallets/add-points', [AdminWalletController::class, 'addPoints']);
    Route::post('/wallets/deduct-points', [AdminWalletController::class, 'deductPoints']);
    Route::post('/wallets/reset', [AdminWalletController::class, 'reset']);
    Route::get('/wallets-stats', [AdminWalletController::class, 'stats']);
    Route::get('/wallet-settings', [AdminWalletController::class, 'settings']);
    Route::put('/wallet-settings', [AdminWalletController::class, 'updateSettings']);
});
