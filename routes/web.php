
<?php

use App\Http\Controllers\Customer\HomeController;
use App\Http\Controllers\Customer\ProductController;
use App\Http\Controllers\Customer\CartController;
use App\Http\Controllers\Customer\CheckoutController;
use App\Http\Controllers\Customer\OrderController;
use App\Http\Controllers\Customer\ReviewController;
use App\Http\Controllers\Customer\WishlistController;
use App\Http\Controllers\Customer\ProfileController;
use App\Http\Controllers\Auth\AuthController;
use Illuminate\Support\Facades\Route;

use Illuminate\Foundation\Auth\EmailVerificationRequest;
use App\Http\Controllers\Vendor\ProductController as VendorProductController;
use App\Http\Controllers\Vendor\OrderController as VendorOrderController;
use App\Http\Controllers\Vendor\ReviewController as VendorReviewController;
use App\Http\Controllers\Vendor\PayoutController as VendorPayoutController;
use App\Http\Controllers\Vendor\SettingsController as VendorSettingsController;
use App\Http\Controllers\Vendor\DashboardController as VendorDashboardController;

// ── Vendors Routes ──────────────────────────────────────────────────────
Route::prefix('vendor')
    ->name('vendor.')
    ->middleware(['auth', 'role:vendor', 'vendor.approved'])
    ->group(function () {

        Route::get('/dashboard', [VendorDashboardController::class, 'index'])->name('dashboard');

        Route::prefix('products')->name('products.')->group(function () {
           Route::get('/', [VendorProductController::class, 'index'])->name('index');
    Route::get('/create', [VendorProductController::class, 'create'])->name('create');
    Route::post('/', [VendorProductController::class, 'store'])->name('store');
  Route::put('/{product:id}', [VendorProductController::class, 'update'])->name('update');
Route::delete('/{product:id}', [VendorProductController::class, 'destroy'])->name('destroy');
Route::get('/{product:id}', [VendorProductController::class, 'edit'])->name('edit');       });

        Route::prefix('orders')->name('orders.')->group(function () {
            Route::get('/', [VendorOrderController::class, 'index'])->name('index');
            Route::get('/{orderItem}', [VendorOrderController::class, 'show'])->name('show');
            Route::post('/{orderItem}/ship', [VendorOrderController::class, 'markShipped'])->name('ship');
            Route::post('/{orderItem}/deliver', [VendorOrderController::class, 'markDelivered'])->name('deliver');
        });

        Route::prefix('reviews')->name('reviews.')->group(function () {
            Route::get('/', [VendorReviewController::class, 'index'])->name('index');
            Route::post('/{review}/reply', [VendorReviewController::class, 'reply'])->name('reply');
        });

        Route::prefix('payouts')->name('payouts.')->group(function () {
            Route::get('/', [VendorPayoutController::class, 'index'])->name('index');
            Route::post('/request', [VendorPayoutController::class, 'request'])->name('request');
        });

        Route::get('/settings', [VendorSettingsController::class, 'edit'])->name('settings');
        Route::put('/settings', [VendorSettingsController::class, 'update'])->name('settings.update');
        Route::get('/settings/payments', [VendorSettingsController::class, 'payments'])->name('settings.payments');
        Route::put('/settings/payments', [VendorSettingsController::class, 'updatePayments'])->name('settings.payments.update');
    });
// ── Public Routes ──────────────────────────────────────────────────────

Route::get('/', [HomeController::class, 'index'])->name('home');
Route::get('/shop', [ProductController::class, 'index'])->name('shop');
Route::get('/shop/{product:slug}', [ProductController::class, 'show'])->name('product.show');
Route::get('/category/{category:slug}', [ProductController::class, 'byCategory'])->name('category.show');
Route::get('/vendor/{vendor:store_slug}', [HomeController::class, 'vendorStore'])->name('vendor.store');
Route::get('/search', [ProductController::class, 'search'])->name('search');

// Cart (guest + auth)
Route::prefix('cart')->name('cart.')->group(function () {
    Route::get('/', [CartController::class, 'index'])->name('index');
    Route::post('/add', [CartController::class, 'add'])->name('add');
    Route::patch('/{item}', [CartController::class, 'update'])->name('update');
    Route::delete('/{item}', [CartController::class, 'remove'])->name('remove');
    Route::get('/count', [CartController::class, 'count'])->name('count');
});

// ── Auth Routes ────────────────────────────────────────────────────────

Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login']);
    Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
    Route::post('/register', [AuthController::class, 'register']);
    Route::get('/forgot-password', [AuthController::class, 'showForgotPassword'])->name('password.request');
    Route::post('/forgot-password', [AuthController::class, 'sendResetLink'])->name('password.email');
    Route::get('/reset-password/{token}', [AuthController::class, 'showResetPassword'])->name('password.reset');
    Route::post('/reset-password', [AuthController::class, 'resetPassword'])->name('password.update');
});

Route::post('/logout', [AuthController::class, 'logout'])->name('logout')->middleware('auth');




// Email Verification Routes
Route::get('/email/verify', function () {
    return view('auth.verify-email');
})->middleware('auth')->name('verification.notice');

Route::get('/email/verify/{id}/{hash}', function (EmailVerificationRequest $request) {
    $request->fulfill();
    return redirect()->route('home');
})->middleware(['auth', 'signed'])->name('verification.verify');

Route::post('/email/verification-notification', function (Request $request) {
    $request->user()->sendEmailVerificationNotification();
    return back()->with('status', 'verification-link-sent');
})->middleware(['auth', 'throttle:6,1'])->name('verification.send');
// ── Authenticated Customer Routes ──────────────────────────────────────

Route::middleware(['auth', 'verified', 'role:customer'])->group(function () {
    // Checkout
    Route::get('/checkout', [CheckoutController::class, 'index'])->name('checkout.index');
    Route::post('/checkout', [CheckoutController::class, 'store'])->name('checkout.store');
    Route::get('/checkout/success/{order}', [CheckoutController::class, 'success'])->name('checkout.success');

    // Payment webhooks / returns
    Route::get('/payment/stripe/return', [CheckoutController::class, 'stripeReturn'])->name('payment.stripe.return');
    Route::get('/payment/paypal/return', [CheckoutController::class, 'paypalReturn'])->name('payment.paypal.return');
    Route::get('/payment/paypal/cancel', [CheckoutController::class, 'paypalCancel'])->name('payment.paypal.cancel');

    // Orders
    Route::prefix('orders')->name('orders.')->group(function () {
        Route::get('/', [OrderController::class, 'index'])->name('index');
        Route::get('/{order}', [OrderController::class, 'show'])->name('show');
        Route::post('/{order}/cancel', [OrderController::class, 'cancel'])->name('cancel');
        Route::get('/{order}/invoice', [OrderController::class, 'invoice'])->name('invoice');
    });

    // Reviews
    Route::post('/products/{product}/reviews', [ReviewController::class, 'store'])->name('reviews.store');
    Route::delete('/reviews/{review}', [ReviewController::class, 'destroy'])->name('reviews.destroy');

    // Wishlist
    Route::post('/wishlist/{product}', [WishlistController::class, 'toggle'])->name('wishlist.toggle');
    Route::get('/wishlist', [WishlistController::class, 'index'])->name('wishlist.index');

    // Profile
    Route::get('/profile', [ProfileController::class, 'show'])->name('profile.show');
    Route::put('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::put('/profile/password', [ProfileController::class, 'updatePassword'])->name('profile.password');
    Route::get('/profile/addresses', [ProfileController::class, 'addresses'])->name('profile.addresses');
    Route::post('/profile/addresses', [ProfileController::class, 'storeAddress'])->name('profile.addresses.store');
});

// ── Vendor Apply Route ─────────────────────────────────────────────────

Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/become-vendor', [\App\Http\Controllers\Vendor\OnboardingController::class, 'show'])->name('vendor.apply');
    Route::post('/become-vendor', [\App\Http\Controllers\Vendor\OnboardingController::class, 'store'])->name('vendor.apply.store');
});

// ── Webhooks (no CSRF) ─────────────────────────────────────────────────

Route::prefix('webhooks')->middleware('throttle:60,1')->group(function () {
    Route::post('/stripe', [\App\Http\Controllers\Webhooks\StripeWebhookController::class, 'handle'])->name('webhooks.stripe');
    Route::post('/paypal', [\App\Http\Controllers\Webhooks\PaypalWebhookController::class, 'handle'])->name('webhooks.paypal');
});
 