<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\DashboardController; // Assuming controller will be in App\Http\Controllers\Admin

/*
|--------------------------------------------------------------------------
| Admin Routes
|--------------------------------------------------------------------------
|
| Here is where you can register admin routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::middleware(['auth:sanctum', config('jetstream.auth_session'), 'verified', 'admin'])
    ->prefix('admin')
    ->name('admin.')
    ->group(function () {
        // Use the Livewire component for the dashboard
        Route::get('/dashboard', \App\Livewire\Admin\Dashboard::class)->name('dashboard');

        // User Management Route
        Route::get('/users', \App\Livewire\Admin\UserManagement::class)->name('users.index');

        // Service Category Management Route
        Route::get('/service-categories', \App\Livewire\Admin\ServiceCategoryManagement::class)->name('service-categories.index');

        // Service Management Route
        Route::get('/services', \App\Livewire\Admin\ServiceManagement::class)->name('services.index');

        // Order Management Route
        Route::get('/orders', \App\Livewire\Admin\OrderManagement::class)->name('orders.index');

        // Page Management (CMS) Route
        Route::get('/pages', \App\Livewire\Admin\PageManagement::class)->name('pages.index');

        // Commission Settings Route
        Route::get('/settings/commission', \App\Livewire\Admin\CommissionSettings::class)->name('settings.commission');

        // Transaction Management Route
        Route::get('/transactions', \App\Livewire\Admin\TransactionManagement::class)->name('transactions.index');

        // Wallet Management Route
        Route::get('/wallets', \App\Livewire\Admin\WalletManagement::class)->name('wallets.index');

        // Geography Management Route
        Route::get('/geography', \App\Livewire\Admin\GeographyManagement::class)->name('geography.index');

        // Subscription Plan Management Route
        Route::get('/subscription-plans', \App\Livewire\Admin\SubscriptionPlanManagement::class)->name('subscription-plans.index');

        // Payout Management Route
        Route::get('/payouts', \App\Livewire\Admin\PayoutManagement::class)->name('payouts.index');

        // Notification Manager Route
        Route::get('/notifications/send', \App\Livewire\Admin\NotificationManager::class)->name('notifications.send');

        // Activity Log Route
        Route::get('/activity-log', \App\Livewire\Admin\ActivityLogViewer::class)->name('activity-log.index');

        // Roles & Permissions Route
        Route::get('/roles-permissions', \App\Livewire\Admin\RolesPermissions::class)->name('roles-permissions.index');

        // Provider Verification Route
        Route::get('/provider-verification', \App\Livewire\Admin\ProviderVerification::class)->name('provider-verification.index');

        // Dispute Management Route
        Route::get('/disputes', \App\Livewire\Admin\DisputeManagement::class)->name('disputes.index');

        // Support Ticket Management Route
        Route::get('/support-tickets', \App\Livewire\Admin\SupportTicketManagement::class)->name('support-tickets.index');

        // FAQ Manager Route
        Route::get('/faqs', \App\Livewire\Admin\FaqManager::class)->name('faqs.index');

        // Coupon Manager Route
        Route::get('/coupons', \App\Livewire\Admin\CouponManager::class)->name('coupons.index');

        // Add routes for other admin sections here later
});
