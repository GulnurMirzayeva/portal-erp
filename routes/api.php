<?php

use App\Http\Controllers\Api\ErpWorkerTypeApiController;
use App\Http\Middleware\VerifyErpApiKey;
use Illuminate\Support\Facades\Route;

Route::prefix('erp')->middleware(VerifyErpApiKey::class)->group(function () {
    Route::get('/worker-types', [ErpWorkerTypeApiController::class, 'index'])
        ->name('api.erp.worker-types.index');
    Route::post('/worker-types/calculate-visual-bonus', [ErpWorkerTypeApiController::class, 'calculateVisualBonus'])
        ->name('api.erp.worker-types.calculate-visual-bonus');
});

