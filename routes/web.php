<?php

use App\Http\Controllers\Admin\AuthController;
use App\Http\Controllers\Web\DriverApplicationController;
use Illuminate\Support\Facades\Route;

// ── Language switch ───────────────────────────────────────────────────────────
Route::post('lang/{locale}', function (string $locale) {
    if (in_array($locale, ['en', 'sw'])) {
        session(['locale' => $locale]);
    }
    return back();
})->name('lang.switch');

// ── Public ───────────────────────────────────────────────────────────────────
Route::get('/', fn () => view('welcome'))->name('home');

Route::get('become-driver',  [DriverApplicationController::class, 'show'])->name('become-driver');
Route::post('become-driver', [DriverApplicationController::class, 'store'])->name('become-driver.store');

Route::get('privacy',        fn () => view('privacy'))->name('privacy');
Route::get('terms',          fn () => view('terms'))->name('terms');

// ── Admin auth (guest) ────────────────────────────────────────────────────────
Route::prefix('admin')->name('admin.')->group(function () {
    Route::get('login',  [AuthController::class, 'showLogin'])->name('login');
    Route::post('login', [AuthController::class, 'login'])->name('login.post');
    Route::post('logout', [AuthController::class, 'logout'])->name('logout');
});

// ── Admin panel (protected) ───────────────────────────────────────────────────
Route::prefix('admin')->name('admin.')->middleware('admin')->group(function () {
    Route::get('/',          fn () => redirect()->route('admin.dashboard'));
    Route::get('dashboard',   \App\Livewire\Admin\Dashboard::class)->name('dashboard');
    Route::get('categories', \App\Livewire\Admin\Categories::class)->name('categories');
    Route::get('vendors',    \App\Livewire\Admin\Vendors::class)->name('vendors');
    Route::get('users',      \App\Livewire\Admin\Users::class)->name('users');
    Route::get('products',   \App\Livewire\Admin\Products::class)->name('products');
    Route::get('orders',     \App\Livewire\Admin\Orders::class)->name('orders');
    Route::get('payouts',    \App\Livewire\Admin\Payouts::class)->name('payouts');
    Route::get('drivers',      \App\Livewire\Admin\Drivers::class)->name('drivers');
    Route::get('transactions', \App\Livewire\Admin\Transactions::class)->name('transactions');
    Route::get('profile',      \App\Livewire\Admin\AdminProfile::class)->name('profile');
    Route::get('admins',       \App\Livewire\Admin\Admins::class)->name('admins');
});
