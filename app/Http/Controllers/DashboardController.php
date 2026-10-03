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

        // 7. Ötən ayla müqayisə (Faiz artımı / azalması)
        $growth = $this->calculateMonthOverMonthGrowth($portalService, $filterMonth, $filterBranch, $kpi['total_revenue']);

        // 8. Filiallar üzrə Müqayisəli Analitika (Cədvəl və Reytinq Qrafiki üçün)
        $branchStats = $this->aggregateBranchStats($sales, $branches, $kpi['total_revenue']);

        // 9. Günlük Gəlir Dinamikası (Area Chart üçün)
        $dailyTrend = $this->calculateDailyTrend($sales, $filterMonth);

        // 10. Ən çox gəlir gətirən TOP Oyunlar
        $topGames = $this->aggregateTopGames($sales, 5);

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
     */
    private function calculateMonthOverMonthGrowth(PortalWebsiteService $portalService, string $filterMonth, ?string $filterBranch, float $currentRevenue): array
    {
        try {
            $prevYm = Carbon::parse($filterMonth . '-01')->subMonth()->format('Y-m');
            $cacheKey = "portal_summary_{$prevYm}_" . ($filterBranch ?? 'all');

            $prevRevenue = Cache::remember($cacheKey, 86400, function () use ($portalService, $prevYm, $filterBranch) {
                $prevReport = $portalService->getAccountingReport([
                    'month' => $prevYm,
                    'branch_id' => $filterBranch,
                ]);
                return (float)($prevReport['summary']['total_revenue'] ?? 0.0);
            });

            if ($prevRevenue > 0) {
                $diff = $currentRevenue - $prevRevenue;
                $percent = round(($diff / $prevRevenue) * 100, 1);
                return [
                    'has_prev' => true,
                    'prev_revenue' => $prevRevenue,
                    'diff' => $diff,
                    'percent' => $percent,
                    'is_positive' => $diff >= 0,
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
            'is_positive' => true,
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
     * Seçilmiş ay üzrə günlər ardıcıllığında gəlir və ödəniş trendini hesablayır.
     */
    private function calculateDailyTrend(array $sales, string $filterMonth): array
    {
        $startDate = Carbon::parse($filterMonth . '-01');
        $daysInMonth = $startDate->daysInMonth;

        // Ayın hər günü üçün sıfır struktur yaradırıq
        $daysData = [];
        for ($d = 1; $d <= $daysInMonth; $d++) {
            $dayKey = sprintf('%02d', $d);
            $daysData[$dayKey] = [
                'day' => $dayKey,
                'date_label' => $dayKey . '.' . $startDate->format('m'),
                'revenue' => 0.0,
                'cash' => 0.0,
                'card' => 0.0,
                'sales_count' => 0,
            ];
        }

        foreach ($sales as $s) {
            $dateStr = $s['date'] ?? '';
            $day = null;

            if (preg_match('/^(\d{2})\.(\d{2})\.(\d{4})$/', $dateStr, $matches)) {
                $day = $matches[1];
            } elseif (preg_match('/^\d{4}-\d{2}-(\d{2})$/', $dateStr, $matches)) {
                $day = $matches[1];
            }

            if ($day && isset($daysData[$day])) {
                $cash = (float)($s['cash_amount'] ?? 0);
                $card = (float)($s['card_amount'] ?? 0);
                $total = (float)($s['total_price'] ?? ($cash + $card));

                $daysData[$day]['revenue'] += $total;
                $daysData[$day]['cash'] += $cash;
                $daysData[$day]['card'] += $card;
                $daysData[$day]['sales_count']++;
            }
        }

        $categories = [];
        $revenueSeries = [];
        $cashSeries = [];
        $cardSeries = [];
        $peakDay = null;
        $maxRevenue = -1;

        foreach ($daysData as $item) {
            $categories[] = $item['day'];
            $revenueSeries[] = round($item['revenue'], 2);
            $cashSeries[] = round($item['cash'], 2);
            $cardSeries[] = round($item['card'], 2);

            if ($item['revenue'] > $maxRevenue && $item['revenue'] > 0) {
                $maxRevenue = $item['revenue'];
                $peakDay = $item;
            }
        }

        return [
            'categories' => $categories,
            'revenue_series' => $revenueSeries,
            'cash_series' => $cashSeries,
            'card_series' => $cardSeries,
            'peak_day' => $peakDay,
            'total_days' => $daysInMonth,
        ];
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
                $rec = $query->first();
                if ($rec && !empty($rec->sales_data)) {
                    return $rec->sales_data;
                }
            } else {
                $localRecords = $query->get();
                if ($localRecords->isNotEmpty()) {
                    $editedBranches = [];
                    foreach ($localRecords as $rec) {
                        if (!empty($rec->sales_data)) {
                            $editedBranches[(string)$rec->branch_id] = $rec->sales_data;
                        }
                    }

                    if (!empty($editedBranches)) {
                        // Əgər redaktə olunmuş filiallar varsa, həmin filialların sətirlərini yenisi ilə əvəz edirik
                        $filteredSales = array_filter($sales, function ($s) use ($editedBranches) {
                            $bId = (string)($s['branch_id'] ?? '');
                            return !isset($editedBranches[$bId]);
                        });

                        foreach ($editedBranches as $branchSales) {
                            foreach ($branchSales as $bs) {
                                $filteredSales[] = $bs;
                            }
                        }

                        return array_values($filteredSales);
                    }
                }
            }
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
