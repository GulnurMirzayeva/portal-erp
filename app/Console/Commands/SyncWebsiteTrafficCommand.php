<?php

namespace App\Console\Commands;

use App\Models\CrmTrafficStatistic;
use App\Services\PortalWebsiteService;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class SyncWebsiteTrafficCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'traffic:sync-ga4 {month? : YYYY-MM formatında ay (məs. 2026-10)}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Google Analytics (GA4) vasitəsilə veb-sayt istifadəçiləri və ziyarət saylarını ERP bazasında saxlayır';

    /**
     * Execute the console command.
     */
    public function handle(PortalWebsiteService $portalService): int
    {
        $targetMonth = $this->argument('month');

        $monthsToSync = [];
        if ($targetMonth) {
            if (!preg_match('/^\d{4}-\d{2}$/', $targetMonth)) {
                $this->error("Yanlış ay formatı: {$targetMonth}. YYYY-MM olmalıdır.");
                return 1;
            }
            $monthsToSync[] = $targetMonth;
        } else {
            // Cari ay
            $currentMonth = Carbon::now()->format('Y-m');
            $monthsToSync[] = $currentMonth;

            // Əgər ayın ilk 3 günüdürsə, ötən ayın son günlərini də tamamlayırıq
            if (Carbon::now()->day <= 3) {
                $prevMonth = Carbon::now()->subMonth()->format('Y-m');
                $monthsToSync[] = $prevMonth;
            }
        }

        $this->info("Google Analytics GA4 sinxronizasiyası başlayır...");

        foreach ($monthsToSync as $ym) {
            $this->line("Aya görə sinxron edilir: {$ym}");

            [$year, $month] = explode('-', $ym);
            $year = (int)$year;
            $month = (int)$month;
            $daysInMonth = Carbon::createFromDate($year, $month, 1)->daysInMonth;

            $regData = $portalService->getWebsiteRegisteredUsers($ym);
            $regDaily = $regData['daily_data'] ?? [];

            $traffic = $portalService->getWebsiteTraffic($ym);
            $gaDaily = $traffic['daily_data'] ?? [];

            $updatedCount = 0;

            for ($d = 1; $d <= $daysInMonth; $d++) {
                $dateStr = sprintf('%04d-%02d-%02d', $year, $month, $d);
                $users = isset($regDaily[$d]) ? (int)$regDaily[$d] : null;
                $visits = isset($gaDaily[$d]['visits']) ? (int)$gaDaily[$d]['visits'] : null;

                if ($users !== null || $visits !== null) {
                    $rec = CrmTrafficStatistic::firstOrNew(['date' => $dateStr]);
                    $rec->year = $year;
                    $rec->month = $month;
                    $rec->day = $d;

                    if ($users !== null) {
                        $rec->website_users = $users;
                    }
                    if ($visits !== null) {
                        $rec->website_visits = $visits;
                    }
                    $rec->save();
                    $updatedCount++;
                }
            }

            $totalReg = $regData['total'] ?? 0;
            $totalVisits = $traffic['total_visits'] ?? 0;
            $this->info("{$ym} üçün {$updatedCount} günün statistikası ERP bazasında saxlanıldı. Qeydiyyatlar: {$totalReg}, Ziyarətlər (GA4): {$totalVisits}");
            Log::info("Traffic sync completed for {$ym}: {$updatedCount} days updated (Reg: {$totalReg}, Visits: {$totalVisits}).");
        }

        $this->info("Sinxronizasiya uğurla tamamlandı!");
        return 0;
    }
}
