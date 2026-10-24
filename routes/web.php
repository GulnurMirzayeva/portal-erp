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
use App\Http\Controllers\AttendanceController;
use App\Http\Controllers\AttendanceScanController;
use App\Http\Controllers\CrmController;
use App\Http\Controllers\CrmInquiryController;
use App\Http\Controllers\CrmTrafficController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ExpenseClassificationController;
use App\Http\Controllers\ExpenseController;
use App\Services\PortalWebsiteService;

// QR Davamiyyət: Mobil Skan və Çap (Açıq Marşrutlar)
Route::prefix('attendance')->name('attendance.')->group(function () {
    Route::get('/scan/{token}', [AttendanceScanController::class, 'showScanPage'])->name('scan');
    Route::post('/scan/{token}/submit', [AttendanceScanController::class, 'submitAttendance'])->name('scan.submit');
    Route::get('/branch/{token}/qr-image', [AttendanceScanController::class, 'qrImage'])->name('qr.image');
    Route::get('/branch/{token}/qr-download', [AttendanceScanController::class, 'qrDownload'])->name('qr.download');
    Route::get('/branch/{token}/qr-print', [AttendanceScanController::class, 'qrPrint'])->name('qr.print');
});

Route::middleware('auth')->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])
        ->middleware('permission:dashboard.view')
        ->name('dashboard');

    // CRM Modulu
    Route::middleware('permission:crm.view')->group(function () {
        Route::get('/crm', [CrmController::class, 'index'])->name('crm.index');

        // Müraciət statistikası (1-ci Cədvəl)
        Route::get('/crm/inquiries', [CrmInquiryController::class, 'index'])->name('crm.inquiries.index');
        Route::post('/crm/inquiries/save', [CrmInquiryController::class, 'save'])
            ->middleware('permission:crm.edit')
            ->name('crm.inquiries.save');
        Route::post('/crm/inquiries/row', [CrmInquiryController::class, 'saveRow'])
            ->middleware('permission:crm.edit')
            ->name('crm.inquiries.saveRow');
        Route::post('/crm/inquiries/add-month', [CrmInquiryController::class, 'addMonth'])
            ->middleware('permission:crm.edit')
            ->name('crm.inquiries.addMonth');
        Route::delete('/crm/inquiries/{id}', [CrmInquiryController::class, 'destroy'])
            ->middleware('permission:crm.delete')
            ->name('crm.inquiries.destroy');

        // Trafik və Sosial Media (6 Qrafik və Gündəlik Qeydiyyat)
        Route::get('/crm/traffic', [CrmTrafficController::class, 'index'])->name('crm.traffic.index');
        Route::post('/crm/traffic/save', [CrmTrafficController::class, 'save'])
            ->middleware('permission:crm.edit')
            ->name('crm.traffic.save');
        Route::post('/crm/traffic/save-row', [CrmTrafficController::class, 'saveRow'])
            ->middleware('permission:crm.edit')
            ->name('crm.traffic.saveRow');
    });

    // Mühasibatlıq: Elektron Qaimələr
    Route::middleware('permission:accounting.view')->group(function () {
        Route::get('/accounting', [AccountingController::class, 'index'])->name('accounting.index');
        Route::post('/accounting/save', [AccountingController::class, 'save'])
            ->middleware('permission:accounting.edit')
            ->name('accounting.save');
        Route::get('/accounting/export', [AccountingController::class, 'export'])->name('accounting.export');
    });

    // Portal Şəkillərini göstərmək üçün proxy/stream marşrutu
    Route::get('/portal-storage/{path}', [AccountingController::class, 'portalStorage'])
        ->where('path', '.*')
        ->name('portal.storage');

    // Mühasibatlıq: Xərclər Cədvəli
    Route::middleware('permission:expenses.view')->group(function () {
        Route::get('/expenses', [ExpenseController::class, 'index'])->name('expenses.index');
        Route::post('/expenses/save', [ExpenseController::class, 'save'])
            ->middleware('permission:expenses.edit')
            ->name('expenses.save');
        Route::post('/expenses/delete', [ExpenseController::class, 'deleteSingle'])
            ->middleware('permission:expenses.delete')
            ->name('expenses.delete');
        Route::post('/expenses/upload-images', [ExpenseController::class, 'uploadImages'])
            ->middleware('permission:expenses.edit')
            ->name('expenses.uploadImages');
        Route::get('/expenses/export', [ExpenseController::class, 'export'])->name('expenses.export');

        // Mühasibatlıq: Xərc Təsnifatları (CRUD)
        Route::resource('expense-classifications', ExpenseClassificationController::class)->except(['show']);
        Route::patch('/expense-classifications/{id}/toggle', [ExpenseClassificationController::class, 'toggleStatus'])
            ->middleware('permission:expense-classifications.edit')
            ->name('expense-classifications.toggle');
    });

    // Davamiyyət İdarəetməsi (Admin və Direktor üçün)
    Route::middleware('role_or_permission:Admin|Director|attendance.view')->prefix('attendance')->name('attendance.')->group(function () {
        Route::get('/', [AttendanceController::class, 'index'])->name('index');
        Route::get('/violations', [AttendanceController::class, 'violations'])->name('violations');
        Route::get('/branches', [AttendanceController::class, 'branches'])->name('branches');
        Route::get('/workers', [AttendanceController::class, 'workers'])->name('workers');
        Route::get('/export', [AttendanceController::class, 'exportExcel'])
            ->middleware('role_or_permission:Admin|Director|attendance.export')
            ->name('export');

        // Yalnız Admin: Sinxronizasiya
        Route::post('/workers/sync', [AttendanceController::class, 'syncFromPortal'])
            ->middleware('role_or_permission:Admin|attendance.sync')
            ->name('workers.sync');

        // Tənzimləmələr (Admin və Direktor üçün limitsiz)
        Route::get('/settings', [AttendanceController::class, 'settings'])->name('settings');
        Route::post('/settings', [AttendanceController::class, 'updateSettings'])->name('settings.update');

        // Yalnız Admin: Filial İdarəetməsi
        Route::middleware('role_or_permission:Admin|attendance.manage-branches')->group(function () {
            Route::post('/branches', [AttendanceController::class, 'storeBranch'])->name('branches.store');
            Route::post('/branches/{id}', [AttendanceController::class, 'updateBranch'])->name('branches.update');
            Route::delete('/branches/{id}', [AttendanceController::class, 'destroyBranch'])->name('branches.destroy');
        });

        // Yalnız Admin: İşçi PIN və Cihaz İdarəetməsi
        Route::middleware('role_or_permission:Admin|attendance.manage-workers')->group(function () {
            Route::post('/workers/{id}/pin', [AttendanceController::class, 'updateWorkerPin'])->name('workers.pin');
            Route::post('/workers/{id}/reset-device', [AttendanceController::class, 'resetWorkerDevice'])->name('workers.resetDevice');
        });
    });
});


Route::get('/forgot-password', [ForgotPasswordController::class, 'showLinkRequestForm'])
    ->middleware('guest')
    ->name('password.request');

Route::post('/forgot-password', [ForgotPasswordController::class, 'sendResetLinkEmail'])
    ->middleware('guest')
    ->name('password.email');