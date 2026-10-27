<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

// Veb-sayt statistikalarının hər gün sonunda və saatbaşı avtomatik ERP bazasında saxlanması
\Illuminate\Support\Facades\Schedule::command('traffic:sync-ga4')
    ->dailyAt('23:55')
    ->withoutOverlapping()
    ->appendOutputTo(storage_path('logs/ga4-sync.log'));

\Illuminate\Support\Facades\Schedule::command('traffic:sync-ga4')
    ->dailyAt('00:05')
    ->withoutOverlapping()
    ->appendOutputTo(storage_path('logs/ga4-sync.log'));

\Illuminate\Support\Facades\Schedule::command('traffic:sync-ga4')
    ->hourly()
    ->withoutOverlapping()
    ->appendOutputTo(storage_path('logs/ga4-sync.log'));

// Çıxış etməyi unudan işçiləri təyin edilmiş vaxtda (məs: 06:00) avtomatik çıxış (logout) edir
\Illuminate\Support\Facades\Schedule::command('attendance:auto-checkout')
    ->everyTenMinutes()
    ->withoutOverlapping()
    ->appendOutputTo(storage_path('logs/attendance-auto-checkout.log'));


