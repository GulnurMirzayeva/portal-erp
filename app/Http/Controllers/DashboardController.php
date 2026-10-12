<?php

namespace App\Http\Controllers;

use App\Models\AccountingRecord;
use App\Services\PortalWebsiteService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

class DashboardController extends Controller
{
    /**
     * Əsas ERP Dashboard & Gəlir-Filial Analitikası.
     */
    public function index(Request $request, PortalWebsiteService $portalService)
    {
        $availableMonths = $this->getAvailableMonths();

        // 1. Ay filtri (default: cari ay)
        $filterMonth = $request->query('month');
        if (empty($filterMonth) || !preg_match('/^\d{4}-\d{2}$/', $filterMonth)) {
            $filterMonth = Carbon::now()->format('Y-m');
        }

        // 2. Filial filtri (default: bütün filiallar)
        $filterBranch = $request->query('branch_id');
        if ($filterBranch === 'all' || empty($filterBranch)) {
            $filterBranch = null;
        }

        // 3. Əgər istifadəçi "Yenilə" (refresh) vurubsa, keş təmizlənir
        $forceRefresh = $request->boolean('refresh');
        $cacheKey = "portal_dashboard_report_{$filterMonth}_" . ($filterBranch ?? 'all');

        if ($forceRefresh) {
            Cache::forget($cacheKey);
        }

        // 4. Filiallar siyahısı ("Ofis" çıxarılır)
        $allBranches = Cache::remember('portal_branches_list', 3600, function () use ($portalService) {
            return $portalService->getBranches();
        });
        $branches = array_values(array_filter($allBranches, function ($b) {
            $name = mb_strtolower($b['name'] ?? '');
            return !str_contains($name, 'ofis') && !str_contains($name, 'office');
        }));

        // 5. Seçilmiş ayın məlumatlarını əldə edirik (keşləmə ilə)
        $isCurrentMonth = ($filterMonth === Carbon::now()->format('Y-m'));
        $cacheTtl = $isCurrentMonth ? 180 : 86400; // cari ay: 3 dəq, keçmiş ay: 24 saat

        $report = Cache::remember($cacheKey, $cacheTtl, function () use ($portalService, $filterMonth, $filterBranch) {
            return $portalService->getAccountingReport([
                'month' => $filterMonth,
                'branch_id' => $filterBranch,
            ]);
        });

        $sales = $report['sales'] ?? [];
        $connected = $report['connected'] ?? false;
        $error = $report['error'] ?? null;

        // Yerli bazada mühasibin redaktə edib yadda saxladığı qeydlər varsa, onları tətbiq edirik
        $sales = $this->applyLocalEdits($sales, $filterMonth, $filterBranch);

        // 6. Əsas KPI və Ümumi Göstəricilərin Hesablanması
        $kpi = $this->calculateKpi($sales);

        // 7. Ötən ayla müqayisə (Faiz artımı / azalması - cari ayda həmin günə qədər olan dövr üzrə)
        $growth = $this->calculateMonthOverMonthGrowth($portalService, $filterMonth, $filterBranch, $kpi['total_revenue'], $forceRefresh);

        // 8. Filiallar üzrə Müqayisəli Analitika (Cədvəl və Reytinq Qrafiki üçün)
        $branchStats = $this->aggregateBranchStats($sales, $branches, $kpi['total_revenue']);

        // Seçilmiş ayın və filialın adları
        $selectedMonthName = $this->getMonthDisplayName($filterMonth, $availableMonths);
        $selectedBranchName = 'Bütün Filiallar';
        if ($filterBranch) {
            foreach ($branches as $b) {
                if ($b['id'] == $filterBranch) {
                    $selectedBranchName = $b['name'];
                    break;
                }
            }
        }

        // 9. Günlük Komanda Sayı Dinamikası (3 Dövrün Müqayisəsi: Cari ay, Ötən ay, Keçən ilin eyni ayı)
        $dailyTrend = $this->calculateDailyTrend(
            $portalService,
            $sales,
            $filterMonth,
            $filterBranch,
            $availableMonths,
            $forceRefresh
        );

        // 10. Ən çox gəlir gətirən TOP Oyunlar
        $topGames = $this->aggregateTopGames($sales, 5);

        // 11. Komandalar (Rezervasiyalar) Status Statistikası (Aylıq və Filial üzrə: Ümumi, Accepted, Rejected, Pending)
        if ($forceRefresh) {
            Cache::forget("portal_reservation_stats_{$filterMonth}_" . ($filterBranch ?? 'all'));
        }
        $reservationStats = $portalService->getReservationStatistics($filterMonth, $filterBranch);

        return view('dashboard.index', [
            'filterMonth' => $filterMonth,
            'filterBranch' => $filterBranch,
            'availableMonths' => $availableMonths,
            'branches' => $branches,
            'selectedMonthName' => $selectedMonthName,
            'selectedBranchName' => $selectedBranchName,
            'connected' => $connected,
            'error' => $error,
            'kpi' => $kpi,
            'growth' => $growth,
            'branchStats' => $branchStats,
            'dailyTrend' => $dailyTrend,
            'topGames' => $topGames,
            'reservationStats' => $reservationStats,
            'isCurrentMonth' => $isCurrentMonth,
            'lastUpdated' => Carbon::now()->format('H:i:s'),
        ]);
    }

    /**
     * Əsas KPI-ları hesablayır.
     */
    private function calculateKpi(array $sales): array
    {
        $totalSales = count($sales);
        $totalRevenue = 0.0;
        $totalCash = 0.0;
        $totalCard = 0.0;
        $totalPlayers = 0;

        foreach ($sales as $s) {
            $cash = (float)($s['cash_amount'] ?? 0);
            $card = (float)($s['card_amount'] ?? 0);
            $price = (float)($s['total_price'] ?? ($cash + $card));
            $players = (int)($s['player_count'] ?? 0);

            $totalRevenue += $price;
            $totalCash += $cash;
            $totalCard += $card;
            $totalPlayers += $players;
        }

        $cashPercent = $totalRevenue > 0 ? round(($totalCash / $totalRevenue) * 100, 1) : 0;
        $cardPercent = $totalRevenue > 0 ? round(($totalCard / $totalRevenue) * 100, 1) : 0;
        $avgTicket = $totalSales > 0 ? ($totalRevenue / $totalSales) : 0.0;
        $avgPlayersPerGame = $totalSales > 0 ? round($totalPlayers / $totalSales, 1) : 0;

        return [
            'total_sales' => $totalSales,
            'total_revenue' => $totalRevenue,
            'total_cash' => $totalCash,
            'total_card' => $totalCard,
            'cash_percent' => $cashPercent,
            'card_percent' => $cardPercent,
            'total_players' => $totalPlayers,
            'avg_ticket' => $avgTicket,
            'avg_players_per_game' => $avgPlayersPerGame,
        ];
    }

    /**
     * Ötən ayla müqayisədə artım/azalma faizini hesablayır.
     * Əgər cari aydırsa, ötən ayın tam ayı ilə deyil, məhz həmin günə qədər olan dövrü (Month-to-Date) ilə müqayisə edir.
     */
    private function calculateMonthOverMonthGrowth(PortalWebsiteService $portalService, string $filterMonth, ?string $filterBranch, float $currentRevenue, bool $forceRefresh = false): array
    {
        try {
            $prevYm = Carbon::parse($filterMonth . '-01')->subMonth()->format('Y-m');
            $isCurrentMonth = ($filterMonth === Carbon::now()->format('Y-m'));
            $dayLimit = $isCurrentMonth ? Carbon::now()->day : null;

            $dayKeyPart = $dayLimit ? "day_{$dayLimit}" : "full";
            $cacheKey = "portal_mom_comparison_{$filterMonth}_{$dayKeyPart}_" . ($filterBranch ?? 'all');

            if ($forceRefresh) {
                Cache::forget($cacheKey);
            }

            $prevRevenue = Cache::remember($cacheKey, $isCurrentMonth ? 180 : 86400, function () use ($portalService, $prevYm, $filterBranch, $dayLimit) {
                $prevReport = $portalService->getAccountingReport([
                    'month' => $prevYm,
                    'branch_id' => $filterBranch,
                ]);

                $prevSales = $prevReport['sales'] ?? [];
                $prevSales = $this->applyLocalEdits($prevSales, $prevYm, $filterBranch);

                if ($dayLimit === null) {
                    return (float)($prevReport['summary']['total_revenue'] ?? 0.0);
                }

                // Cari ayda olduğumuz günə qədər (1-dən $dayLimit-ə qədər) olan gəliri hesablayırıq
                $upToDayRevenue = 0.0;
                foreach ($prevSales as $s) {
                    $dateStr = $s['date'] ?? '';
                    $day = null;
                    if (preg_match('/^(\d{2})\./', $dateStr, $m)) {
                        $day = (int)$m[1];
                    } elseif (preg_match('/^\d{4}-\d{2}-(\d{2})/', $dateStr, $m)) {
                        $day = (int)$m[1];
                    }

                    if ($day !== null && $day <= $dayLimit) {
                        $cash = (float)($s['cash_amount'] ?? 0);
                        $card = (float)($s['card_amount'] ?? 0);
                        $upToDayRevenue += (float)($s['total_price'] ?? ($cash + $card));
                    }
                }

                return $upToDayRevenue;
            });

            if ($prevRevenue > 0) {
                $diff = $currentRevenue - $prevRevenue;
                $percent = round(($diff / $prevRevenue) * 100, 1);
                return [
                    'has_prev' => true,
                    'prev_revenue' => $prevRevenue,
                    'diff' => $diff,
                    'percent' => abs($percent),
                    'raw_percent' => $percent,
                    'is_positive' => $diff >= 0,
                    'day_limit' => $dayLimit,
                    'prev_month_label' => Carbon::parse($prevYm . '-01')->format('m.Y'),
                ];
            }
        } catch (\Exception $e) {
            Log::warning("Error calculating growth: " . $e->getMessage());
        }

        return [
            'has_prev' => false,
            'prev_revenue' => 0.0,
            'diff' => 0.0,
            'percent' => 0.0,
            'raw_percent' => 0.0,
            'is_positive' => true,
            'day_limit' => null,
            'prev_month_label' => '',
        ];
    }

    /**
     * Filiallar üzrə müqayisəli statistikaları formalaşdırır və dövriyyəyə görə sıralayır.
     */
    private function aggregateBranchStats(array $sales, array $branches, float $totalNetworkRevenue): array
    {
        $branchMap = [];
        foreach ($branches as $b) {
            $branchMap[(string)$b['id']] = [
                'id' => (string)$b['id'],
                'name' => $b['name'],
                'sales_count' => 0,
                'revenue' => 0.0,
                'cash' => 0.0,
                'card' => 0.0,
                'players' => 0,
                'avg_price' => 0.0,
                'share_percent' => 0.0,
            ];
        }

        foreach ($sales as $s) {
            $bId = (string)($s['branch_id'] ?? '');
            if (empty($bId) || $bId === '0' || $bId === '#') {
                continue;
            }

            if (!isset($branchMap[$bId])) {
                $bName = trim($s['branch_name'] ?? '');
                if (empty($bName) || $bName === 'Filial #' || str_contains(mb_strtolower($bName), 'ofis') || str_contains(mb_strtolower($bName), 'office')) {
                    continue;
                }
                $branchMap[$bId] = [
                    'id' => $bId,
                    'name' => $bName,
                    'sales_count' => 0,
                    'revenue' => 0.0,
                    'cash' => 0.0,
                    'card' => 0.0,
                    'players' => 0,
                    'avg_price' => 0.0,
                    'share_percent' => 0.0,
                ];
            }

            $cash = (float)($s['cash_amount'] ?? 0);
            $card = (float)($s['card_amount'] ?? 0);
            $total = (float)($s['total_price'] ?? ($cash + $card));
            $players = (int)($s['player_count'] ?? 0);

            $branchMap[$bId]['sales_count']++;
            $branchMap[$bId]['revenue'] += $total;
            $branchMap[$bId]['cash'] += $cash;
            $branchMap[$bId]['card'] += $card;
            $branchMap[$bId]['players'] += $players;
        }

        // Hesablamalar və pay faizi
        foreach ($branchMap as $id => &$data) {
            $data['avg_price'] = $data['sales_count'] > 0 ? ($data['revenue'] / $data['sales_count']) : 0.0;
            $data['share_percent'] = $totalNetworkRevenue > 0 ? round(($data['revenue'] / $totalNetworkRevenue) * 100, 1) : 0.0;
        }
        unset($data);

        // Naməlum və ya filial # kimi qeydləri çıxarırıq, yalnız real filialları saxlayırıq
        $validBranches = array_filter($branchMap, function ($item) {
            return !empty($item['name']) && $item['name'] !== 'Filial #' && !empty($item['id']) && $item['id'] !== '#';
        });

        $sorted = array_values($validBranches);
        usort($sorted, fn($a, $b) => $b['revenue'] <=> $a['revenue']);

        // Reytinq indeksi təyin edilir
        foreach ($sorted as $idx => &$item) {
            $item['rank'] = $idx + 1;
        }
        unset($item);

        return $sorted;
    }

    /**
     * Günlük Komanda (Rezervasiya) Dinamikası — 3 dövrün müqayisəsi:
     * 1. Cari / Seçilmiş ay
     * 2. Ötən ay
     * 3. Keçən ilin həmin ayı
     */
    private function calculateDailyTrend(
        PortalWebsiteService $portalService,
        array $sales,
        string $filterMonth,
        ?string $filterBranch,
        array $availableMonths,
        bool $forceRefresh = false
    ): array {
        $startDate = Carbon::parse($filterMonth . '-01');
        $daysInMonth = $startDate->daysInMonth;

        $prevYm = $startDate->copy()->subMonth()->format('Y-m');
        $lastYearYm = $startDate->copy()->subYear()->format('Y-m');

        $prevMonthName = $this->getMonthDisplayName($prevYm, $availableMonths);
        $lastYearMonthName = $this->getMonthDisplayName($lastYearYm, $availableMonths);

        // 1. Cari / Seçilmiş ayın günbəgün komanda sayı
        $currentCounts = array_fill(1, $daysInMonth, 0);
        foreach ($sales as $s) {
            $day = $this->extractDayFromSale($s);
            if ($day && $day >= 1 && $day <= $daysInMonth) {
                $currentCounts[$day]++;
            }
        }

        // 2. Ötən ayın günbəgün komanda sayı (keşləmə ilə)
        $prevCacheKey = "portal_dashboard_report_{$prevYm}_" . ($filterBranch ?? 'all');
        if ($forceRefresh) {
            Cache::forget($prevCacheKey);
        }
        $prevReport = Cache::remember($prevCacheKey, 86400, function () use ($portalService, $prevYm, $filterBranch) {
            return $portalService->getAccountingReport([
                'month' => $prevYm,
                'branch_id' => $filterBranch,
            ]);
        });
        $prevSales = $this->applyLocalEdits($prevReport['sales'] ?? [], $prevYm, $filterBranch);
        $prevCounts = array_fill(1, $daysInMonth, 0);
        foreach ($prevSales as $s) {
            $day = $this->extractDayFromSale($s);
            if ($day && $day >= 1 && $day <= $daysInMonth) {
                $prevCounts[$day]++;
            }
        }

        // 3. Keçən ilin həmin ayının günbəgün komanda sayı (keşləmə ilə)
        $lastYearCacheKey = "portal_dashboard_report_{$lastYearYm}_" . ($filterBranch ?? 'all');
        if ($forceRefresh) {
            Cache::forget($lastYearCacheKey);
        }
        $lastYearReport = Cache::remember($lastYearCacheKey, 86400, function () use ($portalService, $lastYearYm, $filterBranch) {
            return $portalService->getAccountingReport([
                'month' => $lastYearYm,
                'branch_id' => $filterBranch,
            ]);
        });
        $lastYearSales = $this->applyLocalEdits($lastYearReport['sales'] ?? [], $lastYearYm, $filterBranch);
        $lastYearCounts = array_fill(1, $daysInMonth, 0);
        foreach ($lastYearSales as $s) {
            $day = $this->extractDayFromSale($s);
            if ($day && $day >= 1 && $day <= $daysInMonth) {
                $lastYearCounts[$day]++;
            }
        }

        // Cari ay üçün pik gün
        $peakDay = null;
        $maxCount = -1;
        foreach ($currentCounts as $d => $cnt) {
            if ($cnt > $maxCount && $cnt > 0) {
                $maxCount = $cnt;
                $peakDay = [
                    'day' => sprintf('%02d', $d),
                    'count' => $cnt,
                ];
            }
        }

        $categories = array_map(fn($d) => sprintf('%02d', $d), range(1, $daysInMonth));

        return [
            'categories' => $categories,
            'current_series' => array_values($currentCounts),
            'prev_series' => array_values($prevCounts),
            'last_year_series' => array_values($lastYearCounts),
            'peak_day' => $peakDay,
            'total_days' => $daysInMonth,
            'prev_month_name' => $prevMonthName,
            'last_year_month_name' => $lastYearMonthName,
            'totals' => [
                'current' => array_sum($currentCounts),
                'prev' => array_sum($prevCounts),
                'last_year' => array_sum($lastYearCounts),
            ],
        ];
    }

    /**
     * Qeydin tarixindən gün nömrəsini çıxarır.
     */
    private function extractDayFromSale(array $s): ?int
    {
        $dateStr = $s['date'] ?? '';
        if (preg_match('/^(\d{1,2})\./', $dateStr, $m)) {
            return (int)$m[1];
        }
        if (preg_match('/^\d{4}-\d{2}-(\d{1,2})/', $dateStr, $m)) {
            return (int)$m[1];
        }
        if (preg_match('/^(\d{1,2})-/', $dateStr, $m)) {
            return (int)$m[1];
        }
        return null;
    }

    /**
     * Ən çox gəlir gətirən TOP oyunları təyin edir.
     */
    private function aggregateTopGames(array $sales, int $limit = 5): array
    {
        $games = [];
        foreach ($sales as $s) {
            $name = trim($s['game_name'] ?? '');
            if (empty($name)) {
                $name = 'Naməlum Oyun';
            }

            if (!isset($games[$name])) {
                $games[$name] = [
                    'name' => $name,
                    'sales_count' => 0,
                    'revenue' => 0.0,
                    'players' => 0,
                ];
            }

            $cash = (float)($s['cash_amount'] ?? 0);
            $card = (float)($s['card_amount'] ?? 0);
            $total = (float)($s['total_price'] ?? ($cash + $card));

            $games[$name]['sales_count']++;
            $games[$name]['revenue'] += $total;
            $games[$name]['players'] += (int)($s['player_count'] ?? 0);
        }

        usort($games, fn($a, $b) => $b['revenue'] <=> $a['revenue']);

        return array_slice($games, 0, $limit);
    }

    /**
     * Yerli AccountingRecord-da saxlanmış redaktə qeydlərini tətbiq edir.
     */
    private function applyLocalEdits(array $sales, string $month, ?string $branchId): array
    {
        try {
            $query = AccountingRecord::where('month', $month);
            if ($branchId !== null) {
                $query->where('branch_id', (string)$branchId);
            }
            $localRecords = $query->get();
            if ($localRecords->isEmpty()) {
                return $sales;
            }

            $editedSalesById = [];
            foreach ($localRecords as $rec) {
                if (!empty($rec->sales_data) && is_array($rec->sales_data)) {
                    foreach ($rec->sales_data as $s) {
                        if (!empty($s['id'])) {
                            $editedSalesById[(int)$s['id']] = $s;
                        }
                    }
                }
            }

            if (empty($editedSalesById)) {
                return $sales;
            }

            $mergedSales = [];
            $seenIds = [];
            foreach ($sales as $sale) {
                $id = !empty($sale['id']) ? (int)$sale['id'] : null;
                if ($id && isset($editedSalesById[$id])) {
                    $mergedSales[] = $editedSalesById[$id];
                    $seenIds[$id] = true;
                } else {
                    $mergedSales[] = $sale;
                    if ($id) $seenIds[$id] = true;
                }
            }

            foreach ($editedSalesById as $id => $s) {
                if (!isset($seenIds[$id])) {
                    $mergedSales[] = $s;
                }
            }

            return $mergedSales;
        } catch (\Exception $e) {
            Log::warning("Local edits check failed: " . $e->getMessage());
        }

        return $sales;
    }

    /**
     * Mövcud ayların siyahısı.
     */
    private function getAvailableMonths(): array
    {
        $monthNamesAz = [
            '01' => 'Yanvar', '02' => 'Fevral', '03' => 'Mart', '04' => 'Aprel',
            '05' => 'May', '06' => 'İyun', '07' => 'İyul', '08' => 'Avqust',
            '09' => 'Sentyabr', '10' => 'Oktyabr', '11' => 'Noyabr', '12' => 'Dekabr',
        ];

        $currentYm = Carbon::now()->format('Y-m');
        $minYm = '2025-11';
        $maxYm = $currentYm;

        $cur = Carbon::parse($minYm . '-01');
        $end = Carbon::parse($maxYm . '-01');
        $allMonths = collect();
        while ($cur->lessThanOrEqualTo($end)) {
            $allMonths->push($cur->format('Y-m'));
            $cur->addMonth();
        }

        // Ən yeni aydan köhnəyə doğru sıralayırıq
        return $allMonths->unique()->sortDesc()->values()->map(function ($ym) use ($monthNamesAz) {
            [$y, $m] = explode('-', $ym);
            return [
                'key' => $ym,
                'name' => ($monthNamesAz[$m] ?? $m) . ' ' . $y,
                'year' => $y,
                'month' => $m,
            ];
        })->all();
    }

    /**
     * Ayın Azərbaycan dilində formatlanmış adı.
     */
    private function getMonthDisplayName(string $ym, array $availableMonths): string
    {
        foreach ($availableMonths as $m) {
            if ($m['key'] === $ym) {
                return $m['name'];
            }
        }

        $monthNamesAz = [
            '01' => 'Yanvar', '02' => 'Fevral', '03' => 'Mart', '04' => 'Aprel',
            '05' => 'May', '06' => 'İyun', '07' => 'İyul', '08' => 'Avqust',
            '09' => 'Sentyabr', '10' => 'Oktyabr', '11' => 'Noyabr', '12' => 'Dekabr',
        ];
        $parts = explode('-', $ym);
        return ($monthNamesAz[$parts[1] ?? ''] ?? '') . ' ' . ($parts[0] ?? '');
    }
}
