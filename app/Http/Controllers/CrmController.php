<?php

namespace App\Http\Controllers;

use App\Models\AccountingRecord;
use App\Services\PortalWebsiteService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

class CrmController extends Controller
{
    /**
     * CRM Əsas Statistika Paneli (Komanda, Nəfər və Saat üzrə real vaxt statistikaları).
     */
    public function index(Request $request, PortalWebsiteService $portalService)
    {
        $availableMonths = $this->getAvailableMonths();

        // 1. Ay / Dövr filtrlərinin emalı (Tək və ya Çoxsaylı ay seçimi)
        $selectedMonths = $this->resolveSelectedMonths($request);

        // 2. Filial filtri
        $filterBranch = $request->query('branch_id');
        if ($filterBranch === 'all' || empty($filterBranch)) {
            $filterBranch = 'all';
        }

        // 3. Yenilə (force refresh) yoxlanışı
        $forceRefresh = $request->boolean('refresh');

        // 4. Filiallar siyahısı ("Ofis" çıxarılır)
        $allBranches = Cache::remember('portal_branches_list_crm', 3600, function () use ($portalService) {
            return $portalService->getBranches();
        });
        $branches = array_values(array_filter($allBranches, function ($b) {
            $name = mb_strtolower($b['name'] ?? '');
            return !str_contains($name, 'ofis') && !str_contains($name, 'office');
        }));

        $selectedBranchName = 'Bütün Filiallar';
        if ($filterBranch !== 'all') {
            foreach ($branches as $b) {
                if ((string)$b['id'] === (string)$filterBranch) {
                    $selectedBranchName = $b['name'];
                    break;
                }
            }
        }

        // 5. Seçilmiş ayların satış məlumatlarını çəkirik (API + Keş)
        $allSales = [];
        $apiConnected = true;

        foreach ($selectedMonths as $month) {
            $cacheKey = "crm_sales_{$month}_" . ($filterBranch === 'all' ? 'all' : $filterBranch);
            if ($forceRefresh) {
                Cache::forget($cacheKey);
            }

            $isCurrentMonth = ($month === Carbon::now()->format('Y-m'));
            $cacheTtl = $isCurrentMonth ? 180 : 86400; // cari ay: 3 dəq, keçmiş aylar: 24 saat

            $report = Cache::remember($cacheKey, $cacheTtl, function () use ($portalService, $month, $filterBranch) {
                return $portalService->getAccountingReport([
                    'month' => $month,
                    'branch_id' => $filterBranch === 'all' ? null : $filterBranch,
                ]);
            });

            if (!empty($report['connected'])) {
                $monthSales = $report['sales'] ?? [];
                // Əgər yerli bazada düzəlişlər varsa, tətbiq edirik
                $monthSales = $this->applyLocalEdits($monthSales, $month, $filterBranch === 'all' ? null : $filterBranch);
                $allSales = array_merge($allSales, $monthSales);
            } else {
                $apiConnected = false;
            }
        }

        // 6. Dövrün oxunaqlı adı (Məs: "Oktyabr 2026", "2-ci rüb 2026", "Aprel - İyun 2026")
        $periodDisplay = $this->formatPeriodDisplay($selectedMonths);

        // 7. Statistika 1: Komanda sayı üzrə statistika (faktiki iştirakçı sayı: 2, 3, 4, ..., 10+)
        $teamStats = $this->aggregateTeamCountStats($allSales);

        // 8. Statistika 2: Ümumi nəfər sayı üzrə statistika (faktiki oyunçu sayı * komanda sayı)
        $peopleStats = $this->aggregatePeopleCountStats($allSales, $teamStats['counts']);

        // 9. Statistika 3: Saat üzrə statistika (qeyd olunan saatlarda oynanılan komanda sayı)
        $hourStats = $this->aggregateHourlyStats($allSales);

        // 10. Ümumi KPI xülasəsi
        $kpiSummary = [
            'total_teams' => $teamStats['total'],
            'total_people' => $peopleStats['total'],
            'avg_team_size' => $teamStats['total'] > 0 ? round($peopleStats['total'] / $teamStats['total'], 1) : 0,
            'peak_hour' => $hourStats['peak'],
            'top_team_size' => $teamStats['top_size'],
            'top_people_size' => $peopleStats['top_size'],
        ];

        return view('crm.index', [
            'branches' => $branches,
            'availableMonths' => $availableMonths,
            'filterBranch' => $filterBranch,
            'selectedBranchName' => $selectedBranchName,
            'selectedMonths' => $selectedMonths,
            'periodDisplay' => $periodDisplay,
            'teamStats' => $teamStats,
            'peopleStats' => $peopleStats,
            'hourStats' => $hourStats,
            'kpiSummary' => $kpiSummary,
            'apiConnected' => $apiConnected,
            'totalSalesCount' => count($allSales),
        ]);
    }

    /**
     * Seçilmiş ayları sorğudan təhlil edib təmizləyir.
     */
    protected function resolveSelectedMonths(Request $request): array
    {
        $selectedMonths = [];

        if ($request->has('months')) {
            $raw = $request->input('months');
            if (is_string($raw)) {
                $selectedMonths = explode(',', $raw);
            } elseif (is_array($raw)) {
                $selectedMonths = $raw;
            }
        } elseif ($request->has('month')) {
            $selectedMonths = [$request->query('month')];
        }

        // Yalnız düzgün YYYY-MM formatında olan ayları saxlayırıq
        $clean = array_values(array_unique(array_filter($selectedMonths, function ($m) {
            return is_string($m) && preg_match('/^\d{4}-\d{2}$/', $m);
        })));

        if (empty($clean)) {
            // Defolt cari ay
            $clean = [Carbon::now()->format('Y-m')];
        }

        sort($clean);
        return $clean;
    }

    /**
     * Seçilmiş aylara əsasən dövr başlığını formatlayır.
     * Məs: "Oktyabr 2026", "2-ci rüb 2026", "Aprel - İyun 2026".
     */
    protected function formatPeriodDisplay(array $months): string
    {
        $monthNamesAz = [
            '01' => 'Yanvar', '02' => 'Fevral', '03' => 'Mart', '04' => 'Aprel',
            '05' => 'May', '06' => 'İyun', '07' => 'İyul', '08' => 'Avqust',
            '09' => 'Sentyabr', '10' => 'Oktyabr', '11' => 'Noyabr', '12' => 'Dekabr',
        ];

        if (count($months) === 1) {
            [$y, $m] = explode('-', $months[0]);
            return ($monthNamesAz[$m] ?? $m) . ' ' . $y;
        }

        // Standart rüblərin yoxlanışı
        if (count($months) === 3) {
            [$y1, $m1] = explode('-', $months[0]);
            [$y2, $m2] = explode('-', $months[1]);
            [$y3, $m3] = explode('-', $months[2]);

            if ($y1 === $y2 && $y2 === $y3) {
                if ([$m1, $m2, $m3] === ['01', '02', '03']) return '1-ci rüb ' . $y1;
                if ([$m1, $m2, $m3] === ['04', '05', '06']) return '2-ci rüb ' . $y1;
                if ([$m1, $m2, $m3] === ['07', '08', '09']) return '3-cü rüb ' . $y1;
                if ([$m1, $m2, $m3] === ['10', '11', '12']) return '4-cü rüb ' . $y1;
            }
        }

        // 12 ay seçilibsə
        if (count($months) === 12) {
            [$yFirst] = explode('-', $months[0]);
            [$yLast] = explode('-', end($months));
            if ($yFirst === $yLast) {
                return 'Bütün İl ' . $yFirst;
            }
        }

        // 2 və ya 3 ay ardıcıl
        if (count($months) <= 3) {
            $labels = array_map(function ($ym) use ($monthNamesAz) {
                [$y, $m] = explode('-', $ym);
                return ($monthNamesAz[$m] ?? $m) . ' ' . $y;
            }, $months);
            return implode(', ', $labels);
        }

        // Çoxsaylı aylar
        [$yFirst, $mFirst] = explode('-', $months[0]);
        [$yLast, $mLast] = explode('-', end($months));
        return count($months) . ' ay (' . ($monthNamesAz[$mFirst] ?? $mFirst) . ' ' . $yFirst . ' — ' . ($monthNamesAz[$mLast] ?? $mLast) . ' ' . $yLast . ')';
    }

    /**
     * Statistika 1: Komanda sayı üzrə statistika.
     * Kateqoriyalar: 2, 3, 4, 5, 6, 7, 8, 9, 10+
     */
    protected function aggregateTeamCountStats(array $sales): array
    {
        $categories = ['2', '3', '4', '5', '6', '7', '8', '9', '10+'];
        $counts = array_fill_keys($categories, 0);

        foreach ($sales as $s) {
            $p = (int)($s['player_count'] ?? 0);
            if ($p < 2) {
                continue;
            }
            if ($p >= 10) {
                $counts['10+']++;
            } else {
                $key = (string)$p;
                if (isset($counts[$key])) {
                    $counts[$key]++;
                }
            }
        }

        $total = array_sum($counts);
        $percentages = [];
        $maxCount = 0;
        $topSize = '4';

        foreach ($counts as $cat => $val) {
            $pct = $total > 0 ? round(($val / $total) * 100, 1) : 0;
            $percentages[$cat] = $pct;
            if ($val > $maxCount) {
                $maxCount = $val;
                $topSize = $cat;
            }
        }

        return [
            'categories' => $categories,
            'counts' => $counts,
            'percentages' => $percentages,
            'total' => $total,
            'top_size' => [
                'size' => $topSize,
                'count' => $maxCount,
                'percentage' => $percentages[$topSize] ?? 0,
            ],
        ];
    }

    /**
     * Statistika 2: Ümumi nəfər sayı üzrə statistika.
     * Hər kateqoriya üçün: iştirakçı sayı * komanda sayı (10+ üçün faktiki iştirakçıların cəmi).
     */
    protected function aggregatePeopleCountStats(array $sales, array $teamCounts): array
    {
        $categories = ['2', '3', '4', '5', '6', '7', '8', '9', '10+'];
        $counts = array_fill_keys($categories, 0);

        // 2-dən 9-a qədər riyazi vurma ilə dəqiq hesablanır
        for ($i = 2; $i <= 9; $i++) {
            $key = (string)$i;
            $counts[$key] = ($teamCounts[$key] ?? 0) * $i;
        }

        // 10+ kateqoriyası üçün faktiki iştirakçıların cəmi toplanır
        $plusTenSum = 0;
        foreach ($sales as $s) {
            $p = (int)($s['player_count'] ?? 0);
            if ($p >= 10) {
                $plusTenSum += $p;
            }
        }
        $counts['10+'] = $plusTenSum;

        $total = array_sum($counts);
        $percentages = [];
        $maxCount = 0;
        $topSize = '4';

        foreach ($counts as $cat => $val) {
            $pct = $total > 0 ? round(($val / $total) * 100, 1) : 0;
            $percentages[$cat] = $pct;
            if ($val > $maxCount) {
                $maxCount = $val;
                $topSize = $cat;
            }
        }

        return [
            'categories' => $categories,
            'counts' => $counts,
            'percentages' => $percentages,
            'total' => $total,
            'top_size' => [
                'size' => $topSize,
                'count' => $maxCount,
                'percentage' => $percentages[$topSize] ?? 0,
            ],
        ];
    }

    /**
     * Statistika 3: Saat üzrə statistika.
     * Standart iş saatları: 12:00-dan gecə 05:00-a qədər (lazım gələrsə 10:00 və 11:00 daxil edilir).
     */
    protected function aggregateHourlyStats(array $sales): array
    {
        $standardHours = [
            '12:00', '13:00', '14:00', '15:00', '16:00', '17:00',
            '18:00', '19:00', '20:00', '21:00', '22:00', '23:00',
            '0:00', '1:00', '2:00', '3:00', '4:00', '5:00',
        ];

        $counts = array_fill_keys($standardHours, 0);
        $earlyHours = ['10:00' => 0, '11:00' => 0];

        foreach ($sales as $s) {
            $time = trim($s['time'] ?? '');
            if (empty($time) || $time === '—') {
                continue;
            }

            $parts = explode(':', $time);
            $h = (int)$parts[0];
            $hourKey = $h . ':00';

            if (isset($counts[$hourKey])) {
                $counts[$hourKey]++;
            } elseif (isset($earlyHours[$hourKey])) {
                $earlyHours[$hourKey]++;
            } else {
                $counts[$hourKey] = ($counts[$hourKey] ?? 0) + 1;
            }
        }

        // Əgər 10:00 və ya 11:00 saatlarında rezervlər olubsa, siyahının əvvəlinə əlavə edirik
        $finalCategories = [];
        if ($earlyHours['10:00'] > 0) {
            $finalCategories['10:00'] = $earlyHours['10:00'];
        }
        if ($earlyHours['11:00'] > 0) {
            $finalCategories['11:00'] = $earlyHours['11:00'];
        }

        foreach ($counts as $hr => $c) {
            $finalCategories[$hr] = $c;
        }

        $categories = array_keys($finalCategories);
        $total = array_sum($finalCategories);

        $percentages = [];
        $maxHour = '16:00';
        $maxCount = 0;

        foreach ($finalCategories as $hr => $val) {
            $pct = $total > 0 ? round(($val / $total) * 100, 1) : 0;
            $percentages[$hr] = $pct;
            if ($val > $maxCount) {
                $maxCount = $val;
                $maxHour = $hr;
            }
        }

        return [
            'categories' => $categories,
            'counts' => $finalCategories,
            'percentages' => $percentages,
            'total' => $total,
            'peak' => [
                'hour' => $maxHour,
                'count' => $maxCount,
                'percentage' => $percentages[$maxHour] ?? 0,
            ],
        ];
    }

    /**
     * Yerli bazada mühasibin redaktə etdiyi qeydləri tətbiq edir.
     */
    protected function applyLocalEdits(array $sales, string $month, ?string $branchId): array
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
                    if ($id) {
                        $seenIds[$id] = true;
                    }
                }
            }

            foreach ($editedSalesById as $id => $s) {
                if (!isset($seenIds[$id])) {
                    $mergedSales[] = $s;
                }
            }

            return $mergedSales;
        } catch (\Exception $e) {
            Log::warning("Crm local edits check failed: " . $e->getMessage());
        }

        return $sales;
    }

    /**
     * Mövcud ayların siyahısı.
     */
    protected function getAvailableMonths(): array
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
}
