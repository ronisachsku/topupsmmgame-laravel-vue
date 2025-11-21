<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\SocialAuthController;
use App\Http\Controllers\Api\ServiceController;
use App\Http\Controllers\Api\OrderController;
use App\Http\Controllers\Api\VoucherController;
use App\Http\Controllers\Api\WalletController;
use App\Http\Controllers\Api\PaymentController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
*/

// Public routes
Route::prefix('auth')->group(function () {
    Route::post('/register', [AuthController::class, 'register']);
    Route::post('/login', [AuthController::class, 'login']);
    
    // OAuth Google
    Route::get('/google', [SocialAuthController::class, 'redirectToGoogle']);
    Route::get('/google/callback', [SocialAuthController::class, 'handleGoogleCallback']);
    
    // OAuth Facebook
    Route::get('/facebook', [SocialAuthController::class, 'redirectToFacebook']);
    Route::get('/facebook/callback', [SocialAuthController::class, 'handleFacebookCallback']);
});

// Services (public)
Route::prefix('services')->group(function () {
    Route::get('/', [ServiceController::class, 'index']);
    Route::get('/categories', [ServiceController::class, 'categories']);
    Route::get('/{slug}', [ServiceController::class, 'show']);
});

// Order tracking (public)
Route::get('/orders/{orderNumber}', [OrderController::class, 'show']);

// Voucher validation (public)
Route::post('/vouchers/validate', [VoucherController::class, 'validate']);

// Payment webhook (public)
Route::post('/payments/notification', [PaymentController::class, 'notification']);

// Protected routes
Route::middleware('auth:sanctum')->group(function () {
    // Auth
    Route::post('/auth/logout', [AuthController::class, 'logout']);
    Route::get('/auth/user', [AuthController::class, 'user']);
    Route::put('/auth/profile', [AuthController::class, 'updateProfile']);
    Route::put('/auth/password', [AuthController::class, 'changePassword']);
    
    // Orders
    Route::post('/orders', [OrderController::class, 'store']);
    Route::get('/user/orders', [OrderController::class, 'userOrders']);
    
    // Wallet
    Route::get('/wallet/balance', [WalletController::class, 'balance']);
    Route::post('/wallet/topup', [WalletController::class, 'topup']);
    Route::get('/wallet/transactions', [WalletController::class, 'transactions']);
    
    // Payments
    Route::post('/payments/process', [PaymentController::class, 'process']);
});
