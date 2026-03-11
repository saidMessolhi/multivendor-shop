<?php

use App\Http\Controllers\Vendor\DashboardController;
use App\Http\Controllers\Vendor\ProductController;
use App\Http\Controllers\Vendor\OrderController;
use App\Http\Controllers\Vendor\ReviewController;
use App\Http\Controllers\Vendor\PayoutController;
use App\Http\Controllers\Vendor\SettingsController;
use Illuminate\Support\Facades\Route;

Route::prefix('vendor')
    ->name('vendor.')
    ->middleware(['auth', 'role:vendor', 'vendor.approved'])
    ->group(function () {

        Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

        // Products
        Route::prefix('products')->name('products.')->group(function () {
            Route::get('/', [ProductController::class, 'index'])->name('index');
            Route::get('/create', [ProductController::class, 'create'])->name('create');
            Route::post('/', [ProductController::class, 'store'])->name('store');
            Route::get('/{product:slug}', [ProductController::class, 'edit'])->name('edit');
            Route::put('/{product:slug}', [ProductController::class, 'update'])->name('update');
            Route::delete('/{product:slug}', [ProductController::class, 'destroy'])->name('destroy');
            Route::post('/import', [ProductController::class, 'import'])->name('import');
        });

        // Orders
        Route::prefix('orders')->name('orders.')->group(function () {
            Route::get('/', [OrderController::class, 'index'])->name('index');
            Route::get('/{orderItem}', [OrderController::class, 'show'])->name('show');
            Route::post('/{orderItem}/ship', [OrderController::class, 'markShipped'])->name('ship');
            Route::post('/{orderItem}/deliver', [OrderController::class, 'markDelivered'])->name('deliver');
        });

        // Reviews
        Route::prefix('reviews')->name('reviews.')->group(function () {
            Route::get('/', [ReviewController::class, 'index'])->name('index');
            Route::post('/{review}/reply', [ReviewController::class, 'reply'])->name('reply');
        });

        // Payouts
        Route::prefix('payouts')->name('payouts.')->group(function () {
            Route::get('/', [PayoutController::class, 'index'])->name('index');
            Route::post('/request', [PayoutController::class, 'request'])->name('request');
        });

        // Settings
        Route::get('/settings', [SettingsController::class, 'edit'])->name('settings');
        Route::put('/settings', [SettingsController::class, 'update'])->name('settings.update');
        Route::get('/settings/payments', [SettingsController::class, 'payments'])->name('settings.payments');
        Route::put('/settings/payments', [SettingsController::class, 'updatePayments'])->name('settings.payments.update');
    });
