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

Route::group(['prefix' => 'v1', 'middleware' => 'api.signature'], function () {
    Route::get('/wallet/balance', [ApiController::class, 'getBalance']);
    Route::post('/payouts', [ApiController::class, 'createPayout']);
    Route::get('/payouts/{reference_id}', [ApiController::class, 'getPayout']);
});
