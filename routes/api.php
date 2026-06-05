<?php

use App\Http\Controllers\API\Auth\AuthController;
use App\Http\Controllers\API\Auth\ProfileSetupController;
use App\Http\Controllers\API\CategoryController;
use App\Http\Controllers\API\VendorController;
use App\Http\Controllers\API\ProductController;
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

});
