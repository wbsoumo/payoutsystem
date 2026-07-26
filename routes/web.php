<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PublicController;
use App\Http\Controllers\MerchantAuthController;
use App\Http\Controllers\MerchantDashboardController;
use App\Http\Controllers\AdminAuthController;
use App\Http\Controllers\AdminDashboardController;

/*
|--------------------------------------------------------------------------
| Public Website Routes
|--------------------------------------------------------------------------
*/
// Subdomain Routing for doc.taskbazi.xyz
Route::domain('doc.taskbazi.xyz')->group(function () {
    Route::get('/', [PublicController::class, 'docs'])->name('subdomain.docs');
});

Route::get('/', [PublicController::class, 'home'])->name('home');
Route::get('/features', [PublicController::class, 'features'])->name('features');
Route::get('/pricing', [PublicController::class, 'pricing'])->name('pricing');
Route::get('/docs', [PublicController::class, 'docs'])->name('docs');
Route::get('/developers', [PublicController::class, 'developers'])->name('developers');
Route::get('/about', [PublicController::class, 'about'])->name('about');
Route::get('/security', [PublicController::class, 'security'])->name('security');
Route::get('/compliance', [PublicController::class, 'compliance'])->name('compliance');
Route::get('/contact', [PublicController::class, 'contact'])->name('contact');
Route::post('/contact', [PublicController::class, 'requestAccessStore'])->name('contact.store');
Route::get('/support', [PublicController::class, 'support'])->name('support');
Route::get('/privacy', [PublicController::class, 'privacy'])->name('privacy');
Route::get('/terms', [PublicController::class, 'terms'])->name('terms');
Route::get('/status', [PublicController::class, 'status'])->name('status');

/*
|--------------------------------------------------------------------------
| Merchant Portal Routes
|--------------------------------------------------------------------------
*/
Route::group(['prefix' => 'merchant'], function () {
    // Auth
    Route::get('/login', [MerchantAuthController::class, 'loginForm'])->name('merchant.login');
    Route::post('/login', [MerchantAuthController::class, 'login'])->name('merchant.login.submit');
    Route::post('/logout', [MerchantAuthController::class, 'logout'])->name('merchant.logout');

    // Portal Dashboard
    Route::group(['middleware' => 'merchant.auth'], function () {
        Route::get('/dashboard', [MerchantDashboardController::class, 'index'])->name('merchant.dashboard');
        
        // MAIN
        Route::get('/payouts', [MerchantDashboardController::class, 'payouts'])->name('merchant.payouts');
        Route::post('/payouts', [MerchantDashboardController::class, 'submitPayout'])->name('merchant.payouts.submit');
        Route::get('/ledger', [MerchantDashboardController::class, 'ledger'])->name('merchant.ledger');
        Route::get('/settlements', [MerchantDashboardController::class, 'settlements'])->name('merchant.settlements');
        Route::post('/settlements', [MerchantDashboardController::class, 'requestSettlement'])->name('merchant.settlements.request');

        // PAYMENT SERVICES
        Route::get('/collections', [MerchantDashboardController::class, 'collections'])->name('merchant.collections');
        Route::get('/cc-to-bank', [MerchantDashboardController::class, 'ccToBank'])->name('merchant.cc-to-bank');
        Route::post('/cc-to-bank', [MerchantDashboardController::class, 'processCcToBank'])->name('merchant.cc-to-bank.submit');
        Route::get('/virtual-accounts', [MerchantDashboardController::class, 'virtualAccounts'])->name('merchant.virtual-accounts');
        Route::get('/dynamic-qr', [MerchantDashboardController::class, 'dynamicQr'])->name('merchant.dynamic-qr');
        Route::get('/payment-links', [MerchantDashboardController::class, 'paymentLinks'])->name('merchant.payment-links');
        Route::post('/payment-links', [MerchantDashboardController::class, 'createPaymentLink'])->name('merchant.payment-links.submit');

        // DEVELOPER
        Route::get('/api-docs', [MerchantDashboardController::class, 'apiDocs'])->name('merchant.api-docs');
        Route::post('/api-keys/generate', [MerchantDashboardController::class, 'generateApiKeys'])->name('merchant.api-keys.generate');
        Route::get('/api-keys/download', [MerchantDashboardController::class, 'downloadApiKeys'])->name('merchant.api-keys.download');
        Route::delete('/api-keys/{id}', [MerchantDashboardController::class, 'deleteApiKey'])->name('merchant.api-keys.delete');
        Route::post('/api-keys/ip', [MerchantDashboardController::class, 'addIpWhitelist'])->name('merchant.api-keys.ip.add');
        Route::delete('/api-keys/ip/{id}', [MerchantDashboardController::class, 'deleteIpWhitelist'])->name('merchant.api-keys.ip.delete');
        Route::post('/api-keys/ip/{id}/toggle', [MerchantDashboardController::class, 'toggleIpWhitelist'])->name('merchant.api-keys.ip.toggle');
        Route::get('/webhooks', [MerchantDashboardController::class, 'webhooks'])->name('merchant.webhooks');
        Route::post('/webhooks', [MerchantDashboardController::class, 'updateWebhooks'])->name('merchant.webhooks.update');

        // ACCOUNT
        Route::get('/profile', [MerchantDashboardController::class, 'profile'])->name('merchant.profile');
        Route::post('/profile', [MerchantDashboardController::class, 'profileUpdate'])->name('merchant.profile.update');
        Route::post('/profile/password', [MerchantDashboardController::class, 'changePassword'])->name('merchant.profile.password');
        Route::get('/kyc', [MerchantDashboardController::class, 'kyc'])->name('merchant.kyc');
        Route::post('/kyc', [MerchantDashboardController::class, 'uploadKyc'])->name('merchant.kyc.submit');
        Route::get('/disputes', [MerchantDashboardController::class, 'disputes'])->name('merchant.disputes');
        Route::get('/settings', [MerchantDashboardController::class, 'settings'])->name('merchant.settings');
        Route::post('/settings', [MerchantDashboardController::class, 'updateSettings'])->name('merchant.settings.update');

        // Tickets
        Route::get('/tickets', [MerchantDashboardController::class, 'tickets'])->name('merchant.tickets');
        Route::post('/tickets', [MerchantDashboardController::class, 'createTicket'])->name('merchant.tickets.create');
        Route::get('/tickets/{id}', [MerchantDashboardController::class, 'viewTicket'])->name('merchant.tickets.view');
        Route::post('/tickets/{id}/reply', [MerchantDashboardController::class, 'replyTicket'])->name('merchant.tickets.reply');
    });
});

/*
|--------------------------------------------------------------------------
| Super Admin Portal Routes
|--------------------------------------------------------------------------
*/
Route::group(['prefix' => 'admin'], function () {
    // Auth
    Route::get('/login', [AdminAuthController::class, 'loginForm'])->name('admin.login');
    Route::post('/login', [AdminAuthController::class, 'login'])->name('admin.login.submit');
    Route::post('/logout', [AdminAuthController::class, 'logout'])->name('admin.logout');

    // Admin Console
    Route::group(['middleware' => 'admin.auth'], function () {
        Route::get('/dashboard', [AdminDashboardController::class, 'index'])->name('admin.dashboard');
        
        // Merchants Directory
        Route::get('/merchants', [AdminDashboardController::class, 'merchants'])->name('admin.merchants');
        Route::get('/merchants/{id}', [AdminDashboardController::class, 'merchantView'])->name('admin.merchants.view');
        Route::post('/merchants/{id}/status', [AdminDashboardController::class, 'merchantStatusUpdate'])->name('admin.merchants.status');
        Route::post('/merchants/{id}/kyc', [AdminDashboardController::class, 'merchantKycUpdate'])->name('admin.merchants.kyc');
        Route::post('/merchants/{id}/profile', [AdminDashboardController::class, 'merchantProfileUpdate'])->name('admin.merchants.profile.update');
        Route::post('/merchants/{id}/adjust', [AdminDashboardController::class, 'merchantWalletAdjustment'])->name('admin.merchants.wallet.adjust');
        Route::post('/merchants/user/{id}/impersonate', [AdminDashboardController::class, 'impersonateMerchantUser'])->name('admin.merchants.impersonate');
        Route::post('/merchants/user/{id}/update', [AdminDashboardController::class, 'updateMerchantUser'])->name('admin.merchants.user.update');
        Route::post('/merchants/user/{id}/password', [AdminDashboardController::class, 'changeMerchantUserPassword'])->name('admin.merchants.user.password');

        // Enquiries
        Route::get('/enquiries', [AdminDashboardController::class, 'enquiries'])->name('admin.enquiries');
        Route::post('/enquiries/{id}/convert', [AdminDashboardController::class, 'enquiryConvert'])->name('admin.enquiries.convert');

        // Commissions
        Route::get('/commissions', [AdminDashboardController::class, 'commissions'])->name('admin.commissions');
        Route::post('/commissions', [AdminDashboardController::class, 'commissionStore'])->name('admin.commissions.store');

        // Tickets
        Route::get('/tickets', [AdminDashboardController::class, 'tickets'])->name('admin.tickets');
        Route::get('/tickets/{id}', [AdminDashboardController::class, 'viewTicket'])->name('admin.tickets.view');
        Route::post('/tickets/{id}/reply', [AdminDashboardController::class, 'ticketReply'])->name('admin.tickets.reply');

        // System Logs
        Route::get('/logs/audit', [AdminDashboardController::class, 'auditLogs'])->name('admin.logs.audit');
        Route::get('/logs/api', [AdminDashboardController::class, 'apiLogs'])->name('admin.logs.api');

        // System Settings
        Route::get('/settings', [AdminDashboardController::class, 'settings'])->name('admin.settings');
        Route::post('/settings', [AdminDashboardController::class, 'settingsUpdate'])->name('admin.settings.update');
    });
});
