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
        Route::get('/ledger', [MerchantDashboardController::class, 'ledger'])->name('merchant.ledger');
        
        // Profile
        Route::get('/profile', [MerchantDashboardController::class, 'profile'])->name('merchant.profile');
        Route::post('/profile', [MerchantDashboardController::class, 'profileUpdate'])->name('merchant.profile.update');
        Route::post('/profile/password', [MerchantDashboardController::class, 'changePassword'])->name('merchant.profile.password');
        
        // API Keys & Whitelist
        Route::get('/api-keys', [MerchantDashboardController::class, 'apiKeys'])->name('merchant.api-keys');
        Route::post('/api-keys/generate', [MerchantDashboardController::class, 'generateApiKeys'])->name('merchant.api-keys.generate');
        Route::post('/api-keys/ip', [MerchantDashboardController::class, 'addIpWhitelist'])->name('merchant.api-keys.ip.add');
        Route::delete('/api-keys/ip/{id}', [MerchantDashboardController::class, 'deleteIpWhitelist'])->name('merchant.api-keys.ip.delete');
        Route::post('/api-keys/ip/{id}/toggle', [MerchantDashboardController::class, 'toggleIpWhitelist'])->name('merchant.api-keys.ip.toggle');

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
        Route::post('/merchants/{id}/adjust', [AdminDashboardController::class, 'merchantWalletAdjustment'])->name('admin.merchants.wallet.adjust');

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
    });
});
