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

    // Placeholder route for creating payment intent (e.g., for an order)
    Route::post('/payment-intent/{order}', [PaymentController::class, 'createIntent'])->name('payment.intent');
});
