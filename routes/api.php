<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ApiController;

/*
|--------------------------------------------------------------------------
| REST API Routes
|--------------------------------------------------------------------------
|
| All routes in this file are registered under the /api prefix and are
| protected by the custom ApiSignatureMiddleware ('api.signature').
|
*/

Route::get('/v1/logo/{domain}', [ApiController::class, 'getCompanyLogo']);

Route::group(['prefix' => 'v1', 'middleware' => 'api.signature'], function () {
    Route::get('/wallet/balance', [ApiController::class, 'getBalance']);
    Route::get('/wallet/ledger', [ApiController::class, 'getLedgerLogs']);
    Route::post('/payouts', [ApiController::class, 'createPayout']);
    Route::get('/payouts', [ApiController::class, 'getPayouts']);
    Route::get('/payouts/{reference_id}', [ApiController::class, 'getPayout']);

    // Transaction PIN Management
    Route::post('/pin/setup', [ApiController::class, 'setupPin']);
    Route::post('/pin/verify', [ApiController::class, 'verifyPin']);
    Route::post('/pin/change', [ApiController::class, 'changePin']);
    Route::post('/pin/reset', [ApiController::class, 'resetPin']);
});
