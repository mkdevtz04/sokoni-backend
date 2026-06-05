<?php

use App\Http\Controllers\Admin\AuthController;
use Illuminate\Support\Facades\Route;

// ── Public ───────────────────────────────────────────────────────────────────
Route::get('/', fn () => redirect()->route('admin.login'));

// ── Admin auth (guest) ────────────────────────────────────────────────────────
Route::prefix('admin')->name('admin.')->group(function () {
    Route::get('login',  [AuthController::class, 'showLogin'])->name('login');
    Route::post('login', [AuthController::class, 'login'])->name('login.post');
    Route::post('logout', [AuthController::class, 'logout'])->name('logout');
});

// ── Admin panel (protected) ───────────────────────────────────────────────────
Route::prefix('admin')->name('admin.')->middleware('admin')->group(function () {
    Route::get('/',          fn () => redirect()->route('admin.dashboard'));
    Route::get('dashboard',  \App\Livewire\Admin\Dashboard::class)->name('dashboard');
    Route::get('vendors',    \App\Livewire\Admin\Vendors::class)->name('vendors');
    Route::get('users',      \App\Livewire\Admin\Users::class)->name('users');
    Route::get('products',   \App\Livewire\Admin\Products::class)->name('products');
    Route::get('orders',     \App\Livewire\Admin\Orders::class)->name('orders');
});
