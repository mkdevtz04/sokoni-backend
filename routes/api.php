<?php

use App\Http\Controllers\API\Auth\AuthController;
use App\Http\Controllers\API\Auth\ProfileSetupController;
use App\Http\Controllers\API\CartController;
use App\Http\Controllers\API\CategoryController;
use App\Http\Controllers\API\OrderController;
use App\Http\Controllers\API\PaymentController;
use App\Http\Controllers\API\ProductController;
use App\Http\Controllers\API\VendorController;
use App\Http\Controllers\API\VendorEarningController;
use App\Http\Controllers\API\VendorProfileController;
use App\Http\Controllers\API\VendorProductController;
use App\Http\Controllers\API\VendorOrderController;
use App\Http\Controllers\API\DeviceTokenController;
use App\Http\Controllers\API\WalletController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Public Auth Routes
|--------------------------------------------------------------------------
*/
Route::prefix('auth')->group(function () {
    Route::post('register',        [AuthController::class, 'register']);
    Route::post('login',           [AuthController::class, 'login']);
    Route::post('verify-otp',      [AuthController::class, 'verifyOtp']);
    Route::post('resend-otp',      [AuthController::class, 'resendOtp']);
    Route::post('forgot-password', [AuthController::class, 'forgotPassword']);
    Route::post('reset-password',  [AuthController::class, 'resetPassword']);
});

/*
|--------------------------------------------------------------------------
| Payment Webhook (public — no auth, called by Snippe)
|--------------------------------------------------------------------------
*/
Route::post('payments/callback', [PaymentController::class, 'callback']);

/*
|--------------------------------------------------------------------------
| Public Marketplace Routes
|--------------------------------------------------------------------------
*/
Route::get('categories',                       [CategoryController::class, 'index']);
Route::get('categories/{category}',            [CategoryController::class, 'show']);
Route::get('categories/{category}/products',   [CategoryController::class, 'products']);

Route::get('vendors',                          [VendorController::class, 'index']);
Route::get('vendors/{vendor}',                 [VendorController::class, 'show']);
Route::get('vendors/{vendor}/products',        [VendorController::class, 'products']);

Route::get('products',                         [ProductController::class, 'index']);
Route::get('products/featured',                [ProductController::class, 'featured']);
Route::get('products/{product}',               [ProductController::class, 'show']);

/*
|--------------------------------------------------------------------------
| Protected Routes
|--------------------------------------------------------------------------
*/
Route::middleware('auth:sanctum')->group(function () {

    Route::prefix('auth')->group(function () {
        Route::get('me',     [AuthController::class, 'me']);
        Route::post('logout',[AuthController::class, 'logout']);
    });

    Route::prefix('profile')->group(function () {
        Route::post('setup',    [ProfileSetupController::class, 'setup']);
        Route::post('photo',    [ProfileSetupController::class, 'uploadPhoto']);
        Route::post('location', [ProfileSetupController::class, 'setLocation']);
    });

    // Cart
    Route::prefix('cart')->group(function () {
        Route::get('/',           [CartController::class, 'index']);
        Route::post('add',        [CartController::class, 'add']);
        Route::put('{item}',      [CartController::class, 'update']);
        Route::delete('{item}',   [CartController::class, 'remove']);
        Route::delete('/',        [CartController::class, 'clear']);
    });

    // Orders
    Route::prefix('orders')->group(function () {
        Route::get('/',           [OrderController::class, 'index']);
        Route::post('place',      [OrderController::class, 'place']);
        Route::get('{order}',     [OrderController::class, 'show']);
        Route::post('{order}/cancel', [OrderController::class, 'cancel']);
    });

    // Device tokens (push notifications)
    Route::post('device-token',   [DeviceTokenController::class, 'store']);
    Route::delete('device-token', [DeviceTokenController::class, 'destroy']);

    // Wallet
    Route::prefix('wallet')->group(function () {
        Route::get('/',           [WalletController::class, 'index']);
        Route::post('topup',      [WalletController::class, 'topup']);
    });

    // Vendor application & profile
    Route::prefix('vendor')->group(function () {
        Route::get('my-profile',        [VendorProfileController::class, 'myProfile']);
        Route::post('apply',            [VendorProfileController::class, 'apply']);
        Route::put('my-profile',        [VendorProfileController::class, 'update']);
        Route::post('my-profile/logo',  [VendorProfileController::class, 'uploadLogo']);

        // Vendor product management (approved vendors only)
        Route::prefix('products')->group(function () {
            Route::get('/',            [VendorProductController::class, 'index']);
            Route::post('/',           [VendorProductController::class, 'store']);
            Route::put('{product}',    [VendorProductController::class, 'update']);
            Route::post('{product}',   [VendorProductController::class, 'update']); // multipart form update
            Route::delete('{product}', [VendorProductController::class, 'destroy']);
        });

        // Vendor order management
        Route::prefix('orders')->group(function () {
            Route::get('/',                        [VendorOrderController::class, 'index']);
            Route::put('{order}/status',           [VendorOrderController::class, 'updateStatus']);
        });

        // Vendor earnings & withdrawals
        Route::prefix('earnings')->group(function () {
            Route::get('/',        [VendorEarningController::class, 'index']);
            Route::post('withdraw',[VendorEarningController::class, 'requestWithdrawal']);
        });
    });

});
