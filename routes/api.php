<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\ProductController;
use App\Http\Controllers\Api\CategoryController;
use App\Http\Controllers\Api\CartController;
use App\Http\Controllers\Api\OrderController;
use App\Http\Controllers\Api\ReviewController;
use App\Http\Controllers\Api\WishlistController;
use App\Http\Controllers\Api\ProfileController;
use App\Http\Controllers\Api\Vendor\DashboardController as VendorDashboardController;
use App\Http\Controllers\Api\Vendor\ProductController as VendorProductController;
use App\Http\Controllers\Api\Vendor\OrderController as VendorOrderController;
use App\Http\Controllers\Api\Admin\DashboardController as AdminDashboardController;
use App\Http\Controllers\Api\Admin\UserController as AdminUserController;
use App\Http\Controllers\Api\Admin\VendorController as AdminVendorController;
use App\Http\Controllers\Api\Admin\ProductController as AdminProductController;
use App\Http\Controllers\Api\Admin\OrderController as AdminOrderController;

// ── Public Routes ──────────────────────────────────────────────────────────
Route::post('/register', [AuthController::class, 'register']);
Route::post('/login',    [AuthController::class, 'login']);
Route::post('/forgot-password', [AuthController::class, 'forgotPassword']);
Route::post('/reset-password',  [AuthController::class, 'resetPassword']);

// Products (public)
Route::get('/products',              [ProductController::class, 'index']);
Route::get('/products/{product}',    [ProductController::class, 'show']);
Route::get('/categories',            [CategoryController::class, 'index']);
Route::get('/categories/{category}', [CategoryController::class, 'products']);

// ── Authenticated Routes ───────────────────────────────────────────────────
Route::middleware('auth:sanctum')->group(function () {

    // Auth
    Route::post('/logout',  [AuthController::class, 'logout']);
    Route::get('/me',       [AuthController::class, 'me']);

    // Profile
    Route::get('/profile',          [ProfileController::class, 'show']);
    Route::put('/profile',          [ProfileController::class, 'update']);
    Route::post('/profile/avatar',  [ProfileController::class, 'uploadAvatar']);

    // Cart
    Route::get('/cart',                  [CartController::class, 'index']);
    Route::post('/cart',                 [CartController::class, 'add']);
    Route::put('/cart/{cartItem}',       [CartController::class, 'update']);
    Route::delete('/cart/{cartItem}',    [CartController::class, 'remove']);
    Route::delete('/cart',               [CartController::class, 'clear']);

    // Orders
    Route::get('/orders',          [OrderController::class, 'index']);
    Route::post('/orders',         [OrderController::class, 'store']);
    Route::get('/orders/{order}',  [OrderController::class, 'show']);
    Route::post('/orders/{order}/cancel', [OrderController::class, 'cancel']);

    // Reviews
    Route::post('/products/{product}/reviews', [ReviewController::class, 'store']);
    Route::put('/reviews/{review}',            [ReviewController::class, 'update']);
    Route::delete('/reviews/{review}',         [ReviewController::class, 'destroy']);

    // Wishlist
    Route::get('/wishlist',                    [WishlistController::class, 'index']);
    Route::post('/wishlist/{product}',         [WishlistController::class, 'toggle']);

    // ── Vendor Routes ──────────────────────────────────────────────────────
    Route::middleware('role:vendor')->prefix('vendor')->name('api.vendor.')->group(function () {
        Route::get('/dashboard',        [VendorDashboardController::class, 'index']);
        Route::get('/stats',            [VendorDashboardController::class, 'stats']);

        Route::apiResource('/products', VendorProductController::class);
        Route::post('/products/{product}/toggle-status', [VendorProductController::class, 'toggleStatus']);

        Route::get('/orders',                        [VendorOrderController::class, 'index']);
        Route::get('/orders/{orderItem}',            [VendorOrderController::class, 'show']);
        Route::post('/orders/{orderItem}/ship',      [VendorOrderController::class, 'markShipped']);
        Route::post('/orders/{orderItem}/deliver',   [VendorOrderController::class, 'markDelivered']);
    });

    // ── Admin Routes ───────────────────────────────────────────────────────
    Route::middleware('role:admin')->prefix('admin')->name('api.admin.')->group(function () {
        Route::get('/dashboard',    [AdminDashboardController::class, 'index']);
        Route::get('/stats',        [AdminDashboardController::class, 'stats']);

        Route::apiResource('/users',    AdminUserController::class);
        Route::apiResource('/vendors',  AdminVendorController::class);
        Route::apiResource('/products', AdminProductController::class);
        Route::apiResource('/orders',   AdminOrderController::class);

        Route::post('/vendors/{vendor}/approve',  [AdminVendorController::class, 'approve']);
        Route::post('/vendors/{vendor}/suspend',  [AdminVendorController::class, 'suspend']);
    });
});
