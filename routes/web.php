<?php

use App\Http\Controllers\HomeController;
use App\Http\Controllers\PageController;
use App\Http\Controllers\PaymentController; // Import PaymentController
use App\Http\Controllers\ProviderController;
use Illuminate\Support\Facades\Route;

Route::get('/', [HomeController::class, 'index'])->name('home');

// Route for browsing providers
Route::get('/providers', [ProviderController::class, 'index'])->name('providers.index');

// Route for individual provider profile
Route::get('/providers/{provider}', [ProviderController::class, 'show'])->name('providers.show');

// Route for displaying static pages (must come before auth routes if slugs might conflict)
// Ensure this comes *after* more specific routes like /providers/{provider}
Route::get('/{slug}', [PageController::class, 'show'])->name('page.show');

Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_session'),
    'verified',
])->group(function () {
    Route::get('/dashboard', function () {
        return view('dashboard');
    })->name('dashboard');

    // Seeker-specific routes
    Route::middleware(['auth'])->prefix('seeker')->name('seeker.')->group(function () {
        Route::get('/orders', \App\Livewire\Seeker\OrderHistory::class)->name('orders.index');
        Route::get('/orders/{order}/pay', [PaymentController::class, 'showOrderPaymentPage'])->name('orders.pay'); // Page to choose payment method
        // Add other seeker routes here (e.g., profile, saved providers)
    });

    // Provider-specific routes
    Route::middleware(['auth.provider'])->prefix('provider')->name('provider.')->group(function () {
        Route::get('/dashboard', \App\Livewire\Provider\Dashboard::class)->name('dashboard');
        Route::get('/orders', \App\Livewire\Provider\OrderManagement::class)->name('orders.index');
        Route::get('/available-orders', \App\Livewire\Provider\AvailableOrders::class)->name('orders.available');
        Route::get('/services', \App\Livewire\Provider\ManageServices::class)->name('services.manage');
        Route::get('/subscription', \App\Livewire\Provider\SubscriptionManager::class)->name('subscription.index');
        Route::get('/wallet', \App\Livewire\Provider\WalletPayout::class)->name('wallet.index'); // Added Wallet/Payout route
        // Add other provider routes here (e.g., profile settings)
    });

    // Placeholder route for creating Stripe payment intent (e.g., for an order)
    // Route::post('/payment-intent/{order}', [PaymentController::class, 'createIntent'])->name('payment.intent');

    // PayFast Payment Routes
    Route::prefix('payment/payfast')->name('payment.payfast.')->group(function () {
        // Route to initiate payment for an order and redirect to PayFast
        Route::get('/pay/{order}', [PaymentController::class, 'redirectToPayFast'])->name('pay');
        // PayFast callback routes
        Route::post('/notify', [PaymentController::class, 'handlePayFastITN'])->name('notify'); // ITN Handler
        Route::get('/return', [PaymentController::class, 'handlePayFastReturn'])->name('return'); // Success Return
        Route::get('/cancel', [PaymentController::class, 'handlePayFastCancel'])->name('cancel'); // Cancel Return
    });

    // Flutterwave Payment Routes
    Route::prefix('payment/flutterwave')->name('payment.flutterwave.')->group(function () {
        // Route to initiate payment for an order
        Route::get('/pay/{order}', [PaymentController::class, 'redirectToFlutterwave'])->name('pay');
        // Flutterwave callback route
        Route::get('/callback', [PaymentController::class, 'handleFlutterwaveCallback'])->name('callback');
        // Flutterwave webhook route
        Route::post('/webhook', [PaymentController::class, 'handleFlutterwaveWebhook'])->name('webhook');
    });

    // PayPal Payment Routes
    Route::prefix('payment/paypal')->name('payment.paypal.')->group(function () {
        // Route to initiate payment for an order
        Route::get('/pay/{order}', [PaymentController::class, 'payWithPayPal'])->name('pay');
        // PayPal callback routes
        Route::get('/success', [PaymentController::class, 'handlePayPalSuccess'])->name('success');
        Route::get('/cancel', [PaymentController::class, 'handlePayPalCancel'])->name('cancel');
        // Webhooks can be configured separately if needed via config/paypal.php notify_url
    });

    // Subscription Payment Initiation Routes (Provider Only)
    Route::middleware(['auth.provider'])->prefix('subscription-payment')->name('subscription.payment.')->group(function () {
        Route::get('/payfast/{plan}', [PaymentController::class, 'paySubscriptionWithPayFast'])->name('payfast');
        Route::get('/flutterwave/{plan}', [PaymentController::class, 'paySubscriptionWithFlutterwave'])->name('flutterwave');
        Route::get('/paypal/{plan}', [PaymentController::class, 'paySubscriptionWithPayPal'])->name('paypal');
    });

});
