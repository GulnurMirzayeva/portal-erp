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

            $traffic = $portalService->getWebsiteTraffic($ym);

            if (!($traffic['connected'] ?? false)) {
                $this->warn("{$ym} üçün GA4 bağlantısı uğursuz oldu və ya data tapılmadı.");
                continue;
            }

            $dailyData = $traffic['daily_data'] ?? [];
            $updatedCount = 0;

            foreach ($dailyData as $day => $metrics) {
                $dayNum = (int)$day;
                if ($dayNum < 1 || $dayNum > $daysInMonth) {
                    continue;
                }

                $dateStr = sprintf('%04d-%02d-%02d', $year, $month, $dayNum);
                $users = (int)($metrics['users'] ?? 0);
                $visits = (int)($metrics['visits'] ?? 0);

                if ($users > 0 || $visits > 0) {
                    CrmTrafficStatistic::updateOrCreate(
                        ['date' => $dateStr],
                        [
                            'year' => $year,
                            'month' => $month,
                            'day' => $dayNum,
                            'website_users' => $users,
                            'website_visits' => $visits,
                        ]
                    );
                    $updatedCount++;
                }
            }

            $this->info("{$ym} üçün {$updatedCount} günün statistikası ERP bazasında saxlanıldı. Ümumi istifadəçi: " . ($traffic['total_users'] ?? 0) . ", Ümumi ziyarət: " . ($traffic['total_visits'] ?? 0));
            Log::info("Traffic GA4 sync completed for {$ym}: {$updatedCount} days updated.");
        }

        $this->info("Sinxronizasiya uğurla tamamlandı!");
        return 0;
    }
}
