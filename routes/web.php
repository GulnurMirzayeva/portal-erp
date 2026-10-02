<?php

use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\ForgotPasswordController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return auth()->check()
        ? redirect()->route('dashboard')
        : redirect()->route('login');
});

Route::get('/login', [LoginController::class, 'showLoginForm'])
    ->middleware('guest')
    ->name('login');

Route::post('/login', [LoginController::class, 'login']);

Route::post('/logout', [LoginController::class, 'logout'])
    ->middleware('auth')
    ->name('logout');

use App\Http\Controllers\AccountingController;
use App\Services\PortalWebsiteService;

Route::middleware('auth')->group(function () {
    Route::get('/dashboard', function (PortalWebsiteService $portalService) {
        $report = $portalService->getAccountingReport(['month' => date('Y-m')]);
        return view('dashboard.index', [
            'summary' => $report['summary'] ?? [],
            'connected' => $report['connected'] ?? false,
        ]);
    })->name('dashboard');

    Route::get('/accounting', [AccountingController::class, 'index'])->name('accounting.index');
    Route::post('/accounting/save', [AccountingController::class, 'save'])->name('accounting.save');
    Route::post('/accounting/reset', [AccountingController::class, 'reset'])->name('accounting.reset');
    Route::get('/accounting/export', [AccountingController::class, 'export'])->name('accounting.export');
});

Route::get('/forgot-password', [ForgotPasswordController::class, 'showLinkRequestForm'])
    ->middleware('guest')
    ->name('password.request');

Route::post('/forgot-password', [ForgotPasswordController::class, 'sendResetLinkEmail'])
    ->middleware('guest')
    ->name('password.email');