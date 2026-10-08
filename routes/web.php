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
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ExpenseClassificationController;
use App\Http\Controllers\ExpenseController;
use App\Services\PortalWebsiteService;

Route::middleware('auth')->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // Mühasibatlıq: Elektron Qaimələr
    Route::get('/accounting', [AccountingController::class, 'index'])->name('accounting.index');
    Route::post('/accounting/save', [AccountingController::class, 'save'])->name('accounting.save');
    Route::get('/accounting/export', [AccountingController::class, 'export'])->name('accounting.export');

    // Mühasibatlıq: Xərclər Cədvəli
    Route::get('/expenses', [ExpenseController::class, 'index'])->name('expenses.index');
    Route::post('/expenses/save', [ExpenseController::class, 'save'])->name('expenses.save');
    Route::post('/expenses/delete', [ExpenseController::class, 'deleteSingle'])->name('expenses.delete');
    Route::post('/expenses/upload-images', [ExpenseController::class, 'uploadImages'])->name('expenses.uploadImages');
    Route::get('/expenses/export', [ExpenseController::class, 'export'])->name('expenses.export');

    // Mühasibatlıq: Xərc Təsnifatları (CRUD)
    Route::resource('expense-classifications', ExpenseClassificationController::class)->except(['show']);
    Route::patch('/expense-classifications/{id}/toggle', [ExpenseClassificationController::class, 'toggleStatus'])->name('expense-classifications.toggle');
});


Route::get('/forgot-password', [ForgotPasswordController::class, 'showLinkRequestForm'])
    ->middleware('guest')
    ->name('password.request');

Route::post('/forgot-password', [ForgotPasswordController::class, 'sendResetLinkEmail'])
    ->middleware('guest')
    ->name('password.email');