<?php

namespace App\Services;

use Carbon\Carbon;
use Exception;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class PortalWebsiteService
{
    protected string $baseUrl;
    protected string $apiToken;

    public function __construct()
    {
        $this->baseUrl = rtrim(config('services.portal_website.url', 'http://127.0.0.1:8001'), '/');
        $this->apiToken = config('services.portal_website.token', 'portal_erp_sec_2026_9a8b7c6d5e4f3a2b1c');
    }

    /**
     * PortalWebsite API əlaqəsini yoxlayır.
     */
    public function checkConnection(): bool
    {
        try {
            $response = Http::timeout(3)
                ->withHeaders([
                    'X-ERP-API-KEY' => $this->apiToken,
                    'Accept' => 'application/json',
                ])
                ->get("{$this->baseUrl}/api/erp/branches");

            return $response->successful();
        } catch (Exception $e) {
            return false;
        }
    }

    /**
     * Bütün filialları əldə edir.
     */
    public function getBranches(): array
    {
        try {
            $response = Http::timeout(5)
                ->withHeaders([
                    'X-ERP-API-KEY' => $this->apiToken,
                    'Accept' => 'application/json',
                ])
                ->get("{$this->baseUrl}/api/erp/branches");

            if ($response->successful()) {
                return $response->json('branches', []);
            }
        } catch (Exception $e) {
            Log::warning("PortalWebsite branches fetch failed: " . $e->getMessage());
        }

        return [];
    }

    /**
     * 32 Sütunlu Mühasibatlıq hesabatını əldə edir.
     */
    public function getAccountingReport(array $filters = []): array
    {
        $defaultSummary = [
            'total_sales' => 0,
            'total_revenue' => 0,
            'total_cash' => 0,
            'total_card' => 0,
            'average_price' => 0,
            'total_midnight_bonuses' => 0,
            'total_plus_one_bonuses' => 0,
            'filter_month' => $filters['month'] ?? date('Y-m'),
            'filter_branch_id' => $filters['branch_id'] ?? null,
        ];

        $queryParams = $filters;

        // branch_id: əgər 'all' və ya boşdursa, parametrdən çıxarırıq ki, API bütün filialları versin
        if (isset($queryParams['branch_id']) && ($queryParams['branch_id'] === 'all' || empty($queryParams['branch_id']))) {
            unset($queryParams['branch_id']);
        }

        // month: əgər 'all' istənibsə, date_from və date_to göndəririk ki, ay məhdudiyyəti olmasın
        if (isset($queryParams['month']) && $queryParams['month'] === 'all') {
            unset($queryParams['month']);
            $queryParams['date_from'] = '2020-01-01';
            $queryParams['date_to'] = date('Y-12-31', strtotime('+1 year'));
        }

        // Sınaqdan keçiriləcək API ünvanları (əsas ünvan və ehtiyat live ünvan)
        $urlsToTry = array_values(array_unique(array_filter([
            $this->baseUrl,
            'https://portal.land',
        ])));

        $lastError = null;

        foreach ($urlsToTry as $url) {
            try {
                $response = Http::timeout(35)
                    ->withHeaders([
                        'X-ERP-API-KEY' => $this->apiToken,
                        'Accept' => 'application/json',
                    ])
                    ->get("{$url}/api/erp/accounting-report", $queryParams);

                if ($response->successful()) {
                    $data = $response->json();
                    return [
                        'connected' => true,
                        'summary' => $data['summary'] ?? $defaultSummary,
                        'branches' => $data['branches'] ?? [],
                        'sales' => $this->enrichSalesWithIndividualDiscounts($data['sales'] ?? []),
                    ];
                }

                $lastError = 'API Xətası (' . $response->status() . '): ' . ($response->json('message') ?? 'Sorğu uğursuz oldu.');
            } catch (Exception $e) {
                Log::warning("PortalWebsite API connection error ({$url}): " . $e->getMessage());
                $lastError = "Serverə qoşulmaq mümkün olmadı ({$url}): " . $e->getMessage();
            }
        }

        // 3. Əgər heç bir API ünvanı cavab vermədisə, Direct DB fallback cəhd edirik
        try {
            $dbSales = $this->fetchSalesFromDirectDb($filters);
            if (!empty($dbSales)) {
                $summary = $this->summarizeSalesList($dbSales, $filters);
                return [
                    'connected' => true,
                    'summary' => $summary,
                    'branches' => [],
                    'sales' => $this->enrichSalesWithIndividualDiscounts($dbSales),
                ];
            }
        } catch (Exception $dbEx) {
            Log::warning("Direct DB sales fallback failed: " . $dbEx->getMessage());
        }

        return [
            'connected' => false,
            'error' => $lastError ?? "PortalWebsite serverinə qoşulmaq mümkün olmadı. Serverin işlədiyindən əmin olun.",
            'summary' => $defaultSummary,
            'branches' => [],
            'sales' => [],
        ];
    }

    /**
     * Birbaşa verilənlər bazasından satışları çəkir (API offline olduqda fallback kimi).
     */
    protected function fetchSalesFromDirectDb(array $filters): array
    {
        $branchId = $filters['branch_id'] ?? null;
        $month = $filters['month'] ?? null;
        $dateFrom = $filters['date_from'] ?? null;
        $dateTo = $filters['date_to'] ?? null;

        $query = DB::connection('portal_website')->table('sales')
            ->leftJoin('games', 'sales.game_id', '=', 'games.id')
            ->leftJoin('branches', 'sales.branch_id', '=', 'branches.id')
            ->leftJoin('reservations', 'sales.reservation_id', '=', 'reservations.id')
            ->select([
                'sales.*',
                'games.name as game_name',
                'branches.name as branch_name',
                'reservations.user_phone as reservation_user_phone',
            ]);

        if (!empty($branchId) && $branchId !== 'all') {
            $query->where('sales.branch_id', $branchId);
        }

        if (!empty($dateFrom) && !empty($dateTo)) {
            $query->whereBetween('sales.reservation_date', [$dateFrom, $dateTo]);
        } elseif (!empty($month) && $month !== 'all') {
            $start = Carbon::parse($month . '-01')->startOfMonth()->toDateString();
            $end = Carbon::parse($month . '-01')->endOfMonth()->toDateString();
            $query->whereBetween('sales.reservation_date', [$start, $end]);
        }

        $records = $query->orderBy('sales.reservation_date', 'asc')->orderBy('sales.entry_time', 'asc')->get();
        $formatted = [];

        foreach ($records as $idx => $r) {
            $cash = 0.0;
            $card = 0.0;
            if (!empty($r->payments)) {
                $payArr = is_string($r->payments) ? json_decode($r->payments, true) : (array)$r->payments;
                foreach ((array)$payArr as $p) {
                    if (($p['type'] ?? '') === 'cash') $cash = (float)($p['amount'] ?? 0);
                    if (($p['type'] ?? '') === 'card') $card = (float)($p['amount'] ?? 0);
                }
            }
            $total = (float)($r->total_price ?? ($cash + $card));

            $formatted[] = [
                'row_number' => $idx + 1,
                'id' => $r->id,
                'date' => $r->reservation_date ? Carbon::parse($r->reservation_date)->format('d.m.Y') : '',
                'game_name' => $r->game_name ?? '—',
                'branch_name' => $r->branch_name ?? '—',
                'branch_id' => $r->branch_id,
                'time' => $r->entry_time ? Carbon::parse($r->entry_time)->format('H:i') : ($r->reservation_time ? Carbon::parse($r->reservation_time)->format('H:i') : '—'),
                'player_count' => (int)($r->actual_player_count ?? $r->player_count),
                'price_per_person' => (float)($r->price_per_person ?? 0),
                'cash_amount' => $cash,
                'card_amount' => $card,
                'total_price' => $total,
                'discount_note' => $r->discount_type ?? '',
                'customer_name' => $r->group_leader_name ?? '—',
                'customer_phone' => $r->reservation_user_phone ?? '—',
                'note' => $r->team_review ?? ($r->note ?? ''),
            ];
        }

        return $this->enrichSalesWithIndividualDiscounts($formatted);
    }

    /**
     * Satışlara qrup üzvlərinin fərdi endirimlərini əlavə edir.
     */
    public function enrichSalesWithIndividualDiscounts(array $sales): array
    {
        if (empty($sales)) {
            return [];
        }

        $saleIdsToFetch = [];
        foreach ($sales as $s) {
            $hasInd = !empty($s['individual_discounts']) && is_array($s['individual_discounts']);
            $hasGrp = !empty($s['group_discount_image_urls']);
            if ((!$hasInd || !$hasGrp) && !empty($s['id'])) {
                $saleIdsToFetch[] = (int)$s['id'];
            }
        }

        $dbSales = [];
        $participants = [];
        $adminDiscountsMap = [];

        if (!empty($saleIdsToFetch)) {
            try {
                $dbSales = DB::connection('portal_website')->table('sales')
                    ->whereIn('id', $saleIdsToFetch)
                    ->select(['id', 'users_info', 'group_discount_images'])
                    ->get()
                    ->keyBy('id');

                $participants = DB::connection('portal_website')->table('sale_participants')
                    ->whereIn('sale_id', $saleIdsToFetch)
                    ->get()
                    ->groupBy('sale_id');

                $adminDiscountsMap = DB::connection('portal_website')->table('admin_discounts')
                    ->pluck('name', 'id')
                    ->toArray();
            } catch (\Exception $e) {
                Log::warning("enrichSalesWithIndividualDiscounts db fetch error: " . $e->getMessage());
            }
        }

        $portalBaseUrl = rtrim(config('services.portal_website.url', 'https://portal.land'), '/');

        $resolveImageUrl = function ($img) use ($portalBaseUrl) {
            if (empty($img)) return null;
            if (str_starts_with($img, 'http://') || str_starts_with($img, 'https://')) {
                return $img;
            }
            $clean = ltrim($img, '/');
            // Əgər lokal PortalWebsite storage qovluğunda mövcuddursa, ERP marşrutu vasitəsilə təqdim edirik
            $localWebsitePath = base_path("../../PortalWebsite/storage/app/public/{$clean}");
            if (file_exists($localWebsitePath)) {
                return url('/portal-storage/' . $clean);
            }
            return $portalBaseUrl . '/storage/' . $clean;
        };

        foreach ($sales as &$s) {
            $sId = !empty($s['id']) ? (int)$s['id'] : null;

            // Əgər qrup endirimi üçün şəkil varsa, URL-lərini təyin edirik
            $grpImgs = $s['group_discount_images'] ?? ($sId && isset($dbSales[$sId]) ? ($dbSales[$sId]->group_discount_images ?? null) : null);
            if (!empty($grpImgs) && empty($s['group_discount_image_urls'])) {
                $rawGrp = $grpImgs;
                if (is_string($rawGrp)) {
                    $decoded = json_decode($rawGrp, true);
                    $rawGrp = (json_last_error() === JSON_ERROR_NONE && is_array($decoded)) ? $decoded : [$rawGrp];
                }
                $cleanGrp = is_array($rawGrp) ? array_values(array_filter($rawGrp)) : [];
                $s['group_discount_image_urls'] = array_values(array_filter(array_map($resolveImageUrl, $cleanGrp)));
            }

            // Əgər artıq qeyri-boş individual_discounts mövcuddursa, şəkil URL-lərini təmin edirik
            if (!empty($s['individual_discounts']) && is_array($s['individual_discounts'])) {
                foreach ($s['individual_discounts'] as &$ind) {
                    if (empty($ind['image_urls']) && !empty($ind['images'])) {
                        $rawImgs = $ind['images'];
                        if (is_string($rawImgs)) {
                            $decoded = json_decode($rawImgs, true);
                            $rawImgs = (json_last_error() === JSON_ERROR_NONE && is_array($decoded)) ? $decoded : [$rawImgs];
                        }
                        $cleanImgs = is_array($rawImgs) ? array_values(array_filter($rawImgs)) : [];
                        $ind['image_urls'] = array_values(array_filter(array_map($resolveImageUrl, $cleanImgs)));
                    }
                }
                unset($ind);
                continue;
            }

            $sId = !empty($s['id']) ? (int)$s['id'] : null;
            $indList = [];

            if ($sId && isset($dbSales[$sId]) && !empty($dbSales[$sId]->users_info)) {
                $uInfo = is_string($dbSales[$sId]->users_info)
                    ? (json_decode($dbSales[$sId]->users_info, true) ?: [])
                    : (array)$dbSales[$sId]->users_info;

                $pList = ($participants[$sId] ?? collect())->keyBy('slot_index');

                foreach ($uInfo as $idx => $u) {
                    if (!empty($u['discount_id']) || !empty($u['discount_name'])) {
                        $slot = $idx + 1;
                        $name = trim($u['name'] ?? '');
                        if (empty($name) && isset($pList[$slot])) {
                            $name = trim(($pList[$slot]->name ?? '') . ' ' . ($pList[$slot]->surname ?? ''));
                        }
                        if (empty($name)) {
                            $name = 'Üzv #' . $slot;
                        }

                        $dName = $u['discount_name'] ?? null;
                        if (empty($dName) && !empty($u['discount_id']) && isset($adminDiscountsMap[$u['discount_id']])) {
                            $dName = $adminDiscountsMap[$u['discount_id']];
                        }
                        if (empty($dName)) {
                            $dName = 'Fərdi Endirim';
                        }

                        $rawImgs = $u['images'] ?? ($u['image'] ?? []);
                        if (is_string($rawImgs)) {
                            $decoded = json_decode($rawImgs, true);
                            $rawImgs = (json_last_error() === JSON_ERROR_NONE && is_array($decoded)) ? $decoded : [$rawImgs];
                        }
                        $cleanImgs = is_array($rawImgs) ? array_values(array_filter($rawImgs)) : [];
                        $imageUrls = array_values(array_filter(array_map($resolveImageUrl, $cleanImgs)));

                        $indList[] = [
                            'slot' => $slot,
                            'user_name' => $name,
                            'discount_name' => $dName,
                            'discount_percent' => $u['discount_percent'] ?? 0,
                            'images' => $cleanImgs,
                            'image_urls' => $imageUrls,
                        ];
                    }
                }
            }

            $s['individual_discounts'] = $indList;
        }
        unset($s);

        return $sales;
    }

    protected function summarizeSalesList(array $sales, array $filters): array
    {
        $totalSales = count($sales);
        $totalRevenue = 0;
        $totalCash = 0;
        $totalCard = 0;

        foreach ($sales as $s) {
            $totalRevenue += (float)($s['total_price'] ?? 0);
            $totalCash += (float)($s['cash_amount'] ?? 0);
            $totalCard += (float)($s['card_amount'] ?? 0);
        }

        return [
            'total_sales' => $totalSales,
            'total_revenue' => round($totalRevenue, 2),
            'total_cash' => round($totalCash, 2),
            'total_card' => round($totalCard, 2),
            'average_price' => $totalSales > 0 ? round($totalRevenue / $totalSales, 2) : 0,
            'total_midnight_bonuses' => 0,
            'total_plus_one_bonuses' => 0,
            'filter_month' => $filters['month'] ?? date('Y-m'),
            'filter_branch_id' => $filters['branch_id'] ?? null,
        ];
    }

    /**
     * Filialın oyunlarını əldə edir.
     */
    public function getBranchGames(int $branchId): array
    {
        // 1. Direct DB fallback
        try {
            $games = DB::connection('portal_website')->table('games')
                ->leftJoin('game_translations', function ($join) {
                    $join->on('games.id', '=', 'game_translations.game_id')
                         ->where('game_translations.locale', '=', 'az');
                })
                ->where('games.branch_id', $branchId)
                ->select('games.id', DB::raw('COALESCE(game_translations.name, games.slug) as name'))
                ->orderBy('name')
                ->get()
                ->toArray();

            if (!empty($games)) {
                return array_map(function ($g) {
                    return [
                        'id' => $g->id,
                        'name' => $g->name,
                    ];
                }, $games);
            }
        } catch (Exception $e) {
            Log::warning("PortalWebsite direct games fetch failed: " . $e->getMessage());
        }

        // 2. API fallback
        try {
            $response = Http::timeout(5)
                ->withHeaders([
                    'X-ERP-API-KEY' => $this->apiToken,
                    'Accept' => 'application/json',
                ])
                ->get("{$this->baseUrl}/api/erp/expenses/games", ['branch_id' => $branchId]);

            if ($response->successful()) {
                return $response->json('games', []);
            }
        } catch (Exception $e) {
            Log::warning("PortalWebsite API games fetch failed: " . $e->getMessage());
        }

        return [];
    }

    /**
     * Xərclər hesabatını əldə edir (API + Direct DB fallback).
     */
    public function getExpensesReport(array $filters = []): array
    {
        $defaultSummary = [
            'total_count' => 0,
            'total_cash' => 0,
            'total_card' => 0,
            'grand_total' => 0,
        ];

        // 1. PortalWebsite API vasitəsilə çəkməyə cəhd edirik
        try {
            $response = Http::timeout(15)
                ->withHeaders([
                    'X-ERP-API-KEY' => $this->apiToken,
                    'Accept' => 'application/json',
                ])
                ->get("{$this->baseUrl}/api/erp/expenses", $filters);

            if ($response->successful()) {
                $data = $response->json();
                return [
                    'connected' => true,
                    'summary' => $data['summary'] ?? $defaultSummary,
                    'expenses' => $data['expenses'] ?? [],
                    'games' => $data['games'] ?? [],
                ];
            }
        } catch (Exception $e) {
            Log::warning("PortalWebsite API expenses fetch failed: " . $e->getMessage());
        }

        // 2. Direct DB fallback (Əgər API işləmirsə və ya localdadırsa)
        try {
            $branchId = $filters['branch_id'] ?? null;
            $month = $filters['month'] ?? date('Y-m');

            $query = DB::connection('portal_website')->table('expenses')
                ->leftJoin('games', 'expenses.game_id', '=', 'games.id')
                ->leftJoin('game_translations', function ($join) {
                    $join->on('games.id', '=', 'game_translations.game_id')
                         ->where('game_translations.locale', '=', 'az');
                });

            $selectCols = [
                'expenses.id',
                'expenses.branch_id',
                'expenses.game_id',
                'expenses.date',
                'expenses.title',
                'expenses.note',
                'expenses.amount_cash',
                'expenses.amount_card',
                'expenses.classification',
                'expenses.created_at',
                'expenses.updated_at',
                DB::raw('COALESCE(game_translations.name, games.slug) as game_name'),
            ];

            try {
                if (\Illuminate\Support\Facades\Schema::connection('portal_website')->hasColumn('expenses', 'images')) {
                    $selectCols[] = 'expenses.images';
                }
            } catch (Exception $e) {
                // ignore
            }

            $query->select($selectCols);

            if (!empty($branchId) && $branchId !== 'all') {
                $query->where('expenses.branch_id', $branchId);
            }

            if (!empty($month) && $month !== 'all') {
                $start = Carbon::parse($month . '-01')->startOfMonth()->toDateString();
                $end = Carbon::parse($month . '-01')->endOfMonth()->toDateString();
                $query->whereBetween('expenses.date', [$start, $end]);
            }

            $rows = $query->orderBy('expenses.date', 'asc')->orderBy('expenses.id', 'asc')->get();

            $totalCash = 0;
            $totalCard = 0;
            $formattedExpenses = [];

            foreach ($rows as $idx => $row) {
                $cash = (float)($row->amount_cash ?? 0);
                $card = (float)($row->amount_card ?? 0);
                $rowTotal = $cash + $card;

                $totalCash += $cash;
                $totalCard += $card;

                $rawImgs = $row->images ?? [];
                if (is_string($rawImgs)) {
                    $rawImgs = json_decode($rawImgs, true) ?: [];
                }
                $cleanImgs = is_array($rawImgs) ? array_values(array_filter($rawImgs)) : [];
                $imageUrls = array_map(function ($img) {
                    if (str_starts_with($img, 'http://') || str_starts_with($img, 'https://')) {
                        return $img;
                    }
                    return rtrim(config('services.portal_website.url', 'https://portal.land'), '/') . '/storage/' . ltrim($img, '/');
                }, $cleanImgs);

                $formattedExpenses[] = [
                    'id' => $row->id,
                    'row_num' => $idx + 1,
                    'date' => $row->date ? Carbon::parse($row->date)->format('d.m.Y') : '',
                    'raw_date' => $row->date,
                    'game_id' => $row->game_id,
                    'game_name' => $row->game_name ?? ($row->game_id ? 'Oyun #' . $row->game_id : 'Ümumi'),
                    'title' => $row->title ?? '',
                    'note' => $row->note ?? '',
                    'amount_cash' => $cash > 0 ? $cash : null,
                    'amount_card' => $card > 0 ? $card : null,
                    'total_amount' => $rowTotal,
                    'classification' => $row->classification ?? '',
                    'images' => $cleanImgs,
                    'image_urls' => $imageUrls,
                    'created_at' => !empty($row->created_at) ? Carbon::parse($row->created_at)->format('d.m.Y H:i:s') : null,
                    'raw_created_at' => $row->created_at ?? null,
                    'updated_at' => !empty($row->updated_at) ? Carbon::parse($row->updated_at)->format('d.m.Y H:i:s') : null,
                    'raw_updated_at' => $row->updated_at ?? null,
                ];
            }

            return [
                'connected' => true,
                'summary' => [
                    'total_count' => count($formattedExpenses),
                    'total_cash' => $totalCash,
                    'total_card' => $totalCard,
                    'grand_total' => $totalCash + $totalCard,
                ],
                'expenses' => $formattedExpenses,
                'games' => !empty($branchId) && $branchId !== 'all' ? $this->getBranchGames((int)$branchId) : [],
            ];
        } catch (Exception $e) {
            Log::warning("Direct DB fetch expenses error: " . $e->getMessage());

            return [
                'connected' => false,
                'error' => "Xərcləri çəkmək mümkün olmadı: " . $e->getMessage(),
                'summary' => $defaultSummary,
                'expenses' => [],
                'games' => [],
            ];
        }
    }

    /**
     * Xərcləri PortalWebsite-da yeniləyir və ya yaradır (API + Direct DB fallback).
     */
    public function updateExpenses(array $expenses, $branchId, $month, array $deletedIds = []): array
    {
        // Hər bir xərc üçün game_id mütləq təmin edilir (portal.land API-də Unknown column name xətasının qarşısını almaq üçün)
        $branchGames = null;
        foreach ($expenses as &$expItem) {
            $gid = !empty($expItem['game_id']) && is_numeric($expItem['game_id']) ? (int)$expItem['game_id'] : null;
            $gName = trim($expItem['game_name'] ?? '');
            if (!$gid && !empty($gName) && $gName !== 'Ümumi') {
                if ($branchGames === null) {
                    $branchGames = $this->getBranchGames((int)$branchId);
                }
                foreach ($branchGames as $bg) {
                    if (mb_strtolower(trim($bg['name'] ?? '')) === mb_strtolower($gName)) {
                        $gid = (int)$bg['id'];
                        break;
                    }
                }
            }
            $expItem['game_id'] = $gid;
        }
        unset($expItem);

        // 1. API cəhdi
        try {
            $response = Http::timeout(25)
                ->withHeaders([
                    'X-ERP-API-KEY' => $this->apiToken,
                    'Accept' => 'application/json',
                    'Content-Type' => 'application/json',
                ])
                ->post("{$this->baseUrl}/api/erp/expenses/save", [
                    'expenses' => $expenses,
                    'branch_id' => $branchId,
                    'month' => $month,
                    'deleted_ids' => $deletedIds,
                ]);

            if ($response->successful()) {
                $respData = $response->json();
                $errors = $respData['errors'] ?? [];
                $savedCount = $respData['saved_count'] ?? 0;

                // Əgər göndərilən xərclər var idisə amma xətalar səbəbilə heç biri saxlanılmadısa:
                if (!empty($expenses) && $savedCount === 0 && !empty($errors)) {
                    Log::warning("PortalWebsite API save returned errors: " . implode('; ', $errors));
                    return [
                        'success' => false,
                        'message' => 'Əsas bazada yadda saxlamaq mümkün olmadı: ' . implode('; ', $errors),
                        'errors' => $errors,
                        'expenses' => [],
                    ];
                }

                if (!empty($respData['success'])) {
                    return [
                        'success' => true,
                        'message' => $respData['message'] ?? 'Xərclər uğurla yadda saxlanıldı.',
                        'expenses' => $respData['expenses'] ?? $expenses,
                        'deleted_count' => $respData['deleted_count'] ?? count($deletedIds),
                        'saved_count' => $savedCount,
                    ];
                }
            }
        } catch (Exception $e) {
            Log::warning("PortalWebsite API updateExpenses error: " . $e->getMessage());
        }

        // 2. Direct DB fallback
        try {
            $deletedCount = 0;

            // Silinmiş qeydləri silirik
            if (!empty($deletedIds)) {
                $deletedCount += DB::connection('portal_website')->table('expenses')
                    ->whereIn('id', $deletedIds)
                    ->delete();
            }



            $savedCount = 0;
            $savedExpenses = [];

            foreach ($expenses as $item) {
                $id = !empty($item['id']) && is_numeric($item['id']) ? (int)$item['id'] : null;
                $rowDate = null;
                if (!empty($item['date'])) {
                    try {
                        $rowDate = Carbon::parse($item['date'])->format('Y-m-d');
                    } catch (Exception $e) {
                        $rowDate = date('Y-m-d');
                    }
                }

                $rowImgs = $item['images'] ?? [];
                if (is_string($rowImgs)) {
                    $rowImgs = json_decode($rowImgs, true) ?: [];
                }
                $cleanRowImgs = is_array($rowImgs) ? array_values(array_filter($rowImgs)) : [];

                $data = [
                    'branch_id' => $branchId,
                    'game_id' => !empty($item['game_id']) && $item['game_id'] !== 'general' ? (int)$item['game_id'] : null,
                    'date' => $rowDate ?? date('Y-m-d'),
                    'title' => trim($item['title'] ?? ''),
                    'note' => !empty($item['note']) ? trim($item['note']) : null,
                    'amount_cash' => (float)($item['amount_cash'] ?? 0),
                    'amount_card' => (float)($item['amount_card'] ?? 0),
                    'classification' => $item['classification'] ?? '',
                    'updated_at' => Carbon::now(),
                ];

                try {
                    if (\Illuminate\Support\Facades\Schema::connection('portal_website')->hasColumn('expenses', 'images')) {
                        $data['images'] = !empty($cleanRowImgs) ? json_encode($cleanRowImgs) : null;
                    }
                } catch (Exception $e) {
                    // ignore
                }

                if ($id && $id > 0) {
                    DB::connection('portal_website')->table('expenses')
                        ->where('id', $id)
                        ->update($data);
                    $item['id'] = $id;
                    $item['updated_at'] = Carbon::now()->format('d.m.Y H:i:s');
                    $savedExpenses[] = $item;
                    $savedCount++;
                } else {
                    $data['created_at'] = Carbon::now();
                    $newId = DB::connection('portal_website')->table('expenses')->insertGetId($data);
                    $item['id'] = $newId;
                    $item['created_at'] = Carbon::now()->format('d.m.Y H:i:s');
                    $item['updated_at'] = Carbon::now()->format('d.m.Y H:i:s');
                    $savedExpenses[] = $item;
                    $savedCount++;
                }
            }

            $msg = "{$savedCount} xərc məlumatı ümumi bazada yadda saxlanıldı.";
            if ($deletedCount > 0) {
                $msg .= " ({$deletedCount} silinmiş qeyd bazadan silindi)";
            }

            return [
                'success' => true,
                'message' => $msg,
                'expenses' => $savedExpenses,
                'deleted_count' => $deletedCount,
            ];
        } catch (Exception $e) {
            Log::error("Direct DB update expenses failed: " . $e->getMessage());

            return [
                'success' => false,
                'message' => 'Yadda saxlama xətası: ' . $e->getMessage(),
                'expenses' => $expenses,
            ];
        }
    }

    /**
     * Mühasibin dəyişdirdiyi satış məlumatlarını PortalWebsite API vasitəsilə əsas bazada yeniləyir.
     */
    public function updateSales(array $sales): array
    {
        try {
            $response = Http::timeout(25)
                ->withHeaders([
                    'X-ERP-API-KEY' => $this->apiToken,
                    'Accept' => 'application/json',
                    'Content-Type' => 'application/json',
                ])
                ->post("{$this->baseUrl}/api/erp/accounting-report/save", [
                    'sales' => $sales,
                ]);

            if ($response->successful()) {
                return [
                    'success' => true,
                    'updated_count' => $response->json('updated_count', 0),
                    'message' => $response->json('message', 'Məlumatlar uğurla yeniləndi.'),
                    'errors' => $response->json('errors', []),
                ];
            }

            return [
                'success' => false,
                'updated_count' => 0,
                'message' => 'API Xətası (' . $response->status() . '): ' . ($response->json('message') ?? 'Yeniləmək mümkün olmadı.'),
            ];
        } catch (Exception $e) {
            Log::warning("PortalWebsite API updateSales error: " . $e->getMessage());

            return [
                'success' => false,
                'updated_count' => 0,
                'message' => 'Server ilə əlaqə xətası: ' . $e->getMessage(),
            ];
        }
    }

    /**
     * Xərc şəkillərini PortalWebsite API vasitəsilə yükləyir (və ya fallback).
     */
    public function uploadExpenseImages(array $files): array
    {
        try {
            $req = Http::timeout(30)->withHeaders([
                'X-ERP-API-KEY' => $this->apiToken,
                'Accept' => 'application/json',
            ]);

            foreach ($files as $idx => $file) {
                $req->attach("images[{$idx}]", file_get_contents($file->getRealPath()), $file->getClientOriginalName());
            }

            $response = $req->post("{$this->baseUrl}/api/erp/expenses/upload-images");

            if ($response->successful()) {
                return [
                    'success' => true,
                    'paths' => $response->json('paths', []),
                    'urls' => $response->json('urls', []),
                ];
            }

            return [
                'success' => false,
                'message' => 'API Xətası (' . $response->status() . '): ' . ($response->json('message') ?? 'Şəkilləri yükləmək mümkün olmadı.'),
            ];
        } catch (Exception $e) {
            Log::warning("PortalWebsite API uploadExpenseImages error: " . $e->getMessage());

            // Local fallback
            try {
                $paths = [];
                $urls = [];
                foreach ($files as $file) {
                    $stored = $file->store('expenses', 'public');
                    $paths[] = $stored;
                    $urls[] = asset('storage/' . $stored);
                }
                return [
                    'success' => true,
                    'paths' => $paths,
                    'urls' => $urls,
                ];
            } catch (Exception $fallbackEx) {
                return [
                    'success' => false,
                    'message' => 'Şəkilləri yükləmək mümkün olmadı: ' . $e->getMessage(),
                ];
            }
        }
    }

    /**
     * Tək bir xərci PortalWebsite-dan sil (API + Direct DB fallback).
     */
    public function deleteSingleExpense($id): array
    {
        if (!$id) {
            return ['success' => false, 'message' => 'Xərc ID göstərilməyib.'];
        }

        // 1. API cəhdi
        try {
            $response = Http::timeout(15)
                ->withHeaders([
                    'X-ERP-API-KEY' => $this->apiToken,
                    'Accept' => 'application/json',
                    'Content-Type' => 'application/json',
                ])
                ->post("{$this->baseUrl}/api/erp/expenses/delete", [
                    'id' => (int)$id,
                ]);

            if ($response->successful()) {
                return [
                    'success' => true,
                    'message' => $response->json('message', 'Xərc uğurla silindi.'),
                ];
            }
        } catch (Exception $e) {
            Log::warning("PortalWebsite API deleteExpense error: " . $e->getMessage());
        }

        // 2. Direct DB fallback
        try {
            DB::connection('portal_website')->table('expenses')
                ->where('id', (int)$id)
                ->delete();

            return [
                'success' => true,
                'message' => 'Xərc uğurla silindi.',
            ];
        } catch (Exception $e) {
            Log::error("Direct DB delete expense failed: " . $e->getMessage());
            return [
                'success' => false,
                'message' => 'Silmə xətası: ' . $e->getMessage(),
            ];
        }
    }

    /**
     * Veb-sayt trafik və istifadəçi analitikasını (GA4 və ya Portal API) vasitəsilə əldə edir.
     */
    /**
     * Veb-sayt trafik və istifadəçi analitikasını (GA4 və ya Portal API) vasitəsilə əldə edir.
     */
    public function getWebsiteTraffic(string $month): array
    {
        $cacheKey = "ga4_traffic_{$month}";
        if ($cached = \Illuminate\Support\Facades\Cache::get($cacheKey)) {
            if (!empty($cached['connected']) && !empty($cached['daily_data'])) {
                return $cached;
            }
        }

        // 1. Google Analytics (GA4)-dən birbaşa canlı məlumatları çəkirik
        $gaData = $this->fetchFromGoogleAnalytics($month);
        if (!empty($gaData['connected']) && !empty($gaData['daily_data'])) {
            \Illuminate\Support\Facades\Cache::put($cacheKey, $gaData, 1800);
            return $gaData;
        }

        // 2. Portal Website API vasitəsilə cəhd edirik
        $urlsToTry = array_values(array_unique(array_filter([
            $this->baseUrl,
            'https://portal.land',
        ])));

        foreach ($urlsToTry as $url) {
            try {
                $response = Http::timeout(5)
                    ->withHeaders([
                        'X-ERP-API-KEY' => $this->apiToken,
                        'Accept' => 'application/json',
                    ])
                    ->get("{$url}/api/erp/website-traffic", ['month' => $month]);

                if ($response->successful()) {
                    $data = $response->json();
                    if (!empty($data['daily_data'])) {
                        $res = [
                            'connected' => true,
                            'daily_data' => $data['daily_data'] ?? [],
                            'total_users' => $data['total_users'] ?? 0,
                            'total_visits' => $data['total_visits'] ?? 0,
                        ];
                        \Illuminate\Support\Facades\Cache::put($cacheKey, $res, 1800);
                        return $res;
                    }
                }
            } catch (Exception $e) {
                // Ignore
            }
        }

        return [
            'connected' => false,
            'daily_data' => [],
            'total_users' => 0,
            'total_visits' => 0,
        ];
    }

    /**
     * Veb-saytdan qeydiyyatdan keçən istifadəçilərin sayını (Portal API və ya DB) əldə edir.
     */
    public function getWebsiteRegisteredUsers(string $month): array
    {
        $cacheKey = "website_registered_users_{$month}";
        if ($cached = \Illuminate\Support\Facades\Cache::get($cacheKey)) {
            if (!empty($cached['connected']) && !empty($cached['daily_data'])) {
                return $cached;
            }
        }

        // 1. Portal Website canlı API vasitəsilə çəkirik
        $urlsToTry = array_values(array_unique(array_filter([
            $this->baseUrl,
            'https://portal.land',
        ])));

        foreach ($urlsToTry as $url) {
            try {
                $response = Http::timeout(8)
                    ->withHeaders([
                        'X-ERP-API-KEY' => $this->apiToken,
                        'Accept' => 'application/json',
                    ])
                    ->get("{$url}/api/erp/registered-users", ['month' => $month]);

                if ($response->successful()) {
                    $data = $response->json();
                    if (isset($data['daily_data'])) {
                        $res = [
                            'connected' => true,
                            'total' => (int)($data['total'] ?? 0),
                            'daily_data' => $data['daily_data'] ?? [],
                        ];
                        \Illuminate\Support\Facades\Cache::put($cacheKey, $res, 1800);
                        return $res;
                    }
                }
            } catch (Exception $e) {
                Log::warning("PortalWebsite registered users API error ({$url}): " . $e->getMessage());
            }
        }

        // 2. Direct DB fallback
        try {
            $date = Carbon::parse($month . '-01');
            $start = $date->copy()->startOfMonth()->format('Y-m-d 00:00:00');
            $end = $date->copy()->endOfMonth()->format('Y-m-d 23:59:59');

            $users = DB::connection('portal_website')->table('users')
                ->whereBetween('created_at', [$start, $end])
                ->selectRaw('DAY(created_at) as day, count(*) as count')
                ->groupBy('day')
                ->pluck('count', 'day')
                ->toArray();

            $daysInMonth = $date->daysInMonth;
            $dailyData = [];
            $total = 0;
            for ($d = 1; $d <= $daysInMonth; $d++) {
                $cnt = (int)($users[$d] ?? 0);
                $dailyData[$d] = $cnt;
                $total += $cnt;
            }

            if ($total > 0) {
                $res = [
                    'connected' => true,
                    'total' => $total,
                    'daily_data' => $dailyData,
                ];
                \Illuminate\Support\Facades\Cache::put($cacheKey, $res, 1800);
                return $res;
            }
        } catch (Exception $e) {
            Log::warning("Direct DB registered users query failed: " . $e->getMessage());
        }

        return [
            'connected' => false,
            'total' => 0,
            'daily_data' => [],
        ];
    }

    /**
     * Veb-saytdan aylıq və filial üzrə komanda / rezervasiya statistikasını (ümumi, accepted, rejected, pending) əldə edir.
     */
    public function getReservationStatistics(string $month, ?string $branchId = null): array
    {
        $cacheKey = "portal_reservation_stats_{$month}_" . ($branchId ?? 'all');
        if ($cached = \Illuminate\Support\Facades\Cache::get($cacheKey)) {
            if (!empty($cached['connected'])) {
                return $cached;
            }
        }

        $queryParams = ['month' => $month];
        if (!empty($branchId) && $branchId !== 'all') {
            $queryParams['branch_id'] = $branchId;
        }

        // 1. Portal Website canlı API
        $urlsToTry = array_values(array_unique(array_filter([
            $this->baseUrl,
            'https://portal.land',
        ])));

        foreach ($urlsToTry as $url) {
            try {
                $response = Http::timeout(8)
                    ->withHeaders([
                        'X-ERP-API-KEY' => $this->apiToken,
                        'Accept' => 'application/json',
                    ])
                    ->get("{$url}/api/erp/reservation-statistics", $queryParams);

                if ($response->successful()) {
                    $data = $response->json();
                    if (isset($data['total'])) {
                        $res = [
                            'connected' => true,
                            'total' => (int)($data['total'] ?? 0),
                            'accepted' => (int)($data['accepted'] ?? 0),
                            'rejected' => (int)($data['rejected'] ?? 0),
                            'pending' => (int)($data['pending'] ?? 0),
                            'accepted_percent' => (float)($data['accepted_percent'] ?? 0),
                            'rejected_percent' => (float)($data['rejected_percent'] ?? 0),
                            'pending_percent' => (float)($data['pending_percent'] ?? 0),
                        ];
                        \Illuminate\Support\Facades\Cache::put($cacheKey, $res, 180);
                        return $res;
                    }
                }
            } catch (Exception $e) {
                Log::warning("PortalWebsite reservation statistics API error ({$url}): " . $e->getMessage());
            }
        }

        // 2. Direct DB fallback
        try {
            $date = Carbon::parse($month . '-01');
            $startDate = $date->copy()->startOfMonth()->format('Y-m-d');
            $endDate = $date->copy()->endOfMonth()->format('Y-m-d');

            $query = DB::connection('portal_website')->table('reservations')
                ->whereBetween('reservation_date', [$startDate, $endDate]);

            if (!empty($branchId) && $branchId !== 'all') {
                $query->where('branch_id', $branchId);
            }

            $rawCounts = $query->selectRaw('status, count(*) as count')
                ->groupBy('status')
                ->pluck('count', 'status')
                ->toArray();

            $accepted = (int)($rawCounts['accepted'] ?? 0);
            $rejected = (int)($rawCounts['rejected'] ?? 0);
            $pending = (int)(($rawCounts['waiting'] ?? 0) + ($rawCounts['pending'] ?? 0));
            $total = $accepted + $rejected + $pending;

            foreach ($rawCounts as $st => $cnt) {
                if (!in_array($st, ['accepted', 'rejected', 'waiting', 'pending'])) {
                    $total += (int)$cnt;
                }
            }

            $res = [
                'connected' => true,
                'total' => $total,
                'accepted' => $accepted,
                'rejected' => $rejected,
                'pending' => $pending,
                'accepted_percent' => $total > 0 ? round(($accepted / $total) * 100, 1) : 0,
                'rejected_percent' => $total > 0 ? round(($rejected / $total) * 100, 1) : 0,
                'pending_percent' => $total > 0 ? round(($pending / $total) * 100, 1) : 0,
            ];
            \Illuminate\Support\Facades\Cache::put($cacheKey, $res, 180);
            return $res;
        } catch (Exception $e) {
            Log::warning("Direct DB reservation query failed: " . $e->getMessage());
        }

        return [
            'connected' => false,
            'total' => 0,
            'accepted' => 0,
            'rejected' => 0,
            'pending' => 0,
            'accepted_percent' => 0,
            'rejected_percent' => 0,
            'pending_percent' => 0,
        ];
    }


    /**
     * Google Analytics (GA4) API vasitəsilə birbaşa aylıq trafik və istifadəçiləri əldə edir.
     */
    protected function fetchFromGoogleAnalytics(string $month): array
    {
        try {
            $credsPath = config('services.google_analytics.credentials', storage_path('app/google/analytics.json'));
            if (!file_exists($credsPath)) {
                $fallbackPath = base_path('../PortalWebsite/storage/app/google/analytics.json');
                if (file_exists($fallbackPath)) {
                    $credsPath = $fallbackPath;
                } else {
                    Log::warning("GA4 credentials file not found at: {$credsPath}");
                    return ['connected' => false, 'daily_data' => [], 'total_users' => 0, 'total_visits' => 0];
                }
            }

            $creds = json_decode(file_get_contents($credsPath), true);
            if (empty($creds['client_email']) || empty($creds['private_key'])) {
                Log::warning("GA4 credentials file invalid format or missing keys.");
                return ['connected' => false, 'daily_data' => [], 'total_users' => 0, 'total_visits' => 0];
            }

            $propertyId = config('services.google_analytics.property_id', env('GA4_PROPERTY_ID', '519908149'));
            $start = Carbon::parse($month . '-01')->startOfMonth()->format('Y-m-d');
            $end = Carbon::parse($month . '-01')->endOfMonth()->format('Y-m-d');

            // JWT token yaradılması
            $now = time();
            $b64 = fn($str) => rtrim(strtr(base64_encode($str), '+/', '-_'), '=');
            $header = $b64(json_encode(['alg' => 'RS256', 'typ' => 'JWT']));
            $claim = $b64(json_encode([
                'iss' => $creds['client_email'],
                'scope' => 'https://www.googleapis.com/auth/analytics.readonly',
                'aud' => 'https://oauth2.googleapis.com/token',
                'exp' => $now + 3600,
                'iat' => $now,
            ]));
            $data = $header . '.' . $claim;
            openssl_sign($data, $signature, $creds['private_key'], OPENSSL_ALGO_SHA256);
            $jwt = $data . '.' . $b64($signature);

            $tokenResp = Http::timeout(10)->asForm()->post('https://oauth2.googleapis.com/token', [
                'grant_type' => 'urn:ietf:params:oauth:grant-type:jwt-bearer',
                'assertion' => $jwt,
            ]);

            if ($tokenResp->failed()) {
                Log::warning("GA4 OAuth token request failed: " . $tokenResp->body());
                return ['connected' => false, 'daily_data' => [], 'total_users' => 0, 'total_visits' => 0];
            }

            $token = $tokenResp->json('access_token');
            if (empty($token)) {
                Log::warning("GA4 OAuth access_token missing in response: " . $tokenResp->body());
                return ['connected' => false, 'daily_data' => [], 'total_users' => 0, 'total_visits' => 0];
            }

            $gaResp = Http::timeout(15)->withToken($token)->post("https://analyticsdata.googleapis.com/v1beta/properties/{$propertyId}:runReport", [
                'dateRanges' => [['startDate' => $start, 'endDate' => $end]],
                'metrics' => [
                    ['name' => 'screenPageViews'],
                    ['name' => 'activeUsers'],
                ],
                'dimensions' => [['name' => 'date']],
            ]);

            if (!$gaResp->successful()) {
                Log::warning("GA4 runReport request failed: " . $gaResp->body());
                return ['connected' => false, 'daily_data' => [], 'total_users' => 0, 'total_visits' => 0];
            }

            $rows = $gaResp->json('rows', []);
            $dailyData = [];
            $totalViews = 0;
            $totalUsers = 0;

            foreach ($rows as $r) {
                $rawDate = $r['dimensionValues'][0]['value'] ?? ''; // YYYYMMDD
                $day = (int)substr($rawDate, 6, 2);
                $views = (int)($r['metricValues'][0]['value'] ?? 0);
                $users = (int)($r['metricValues'][1]['value'] ?? 0);

                $dailyData[$day] = [
                    'users' => $users,
                    'visits' => $views,
                ];

                $totalViews += $views;
                $totalUsers += $users;
            }

            return [
                'connected' => true,
                'daily_data' => $dailyData,
                'total_users' => $totalUsers,
                'total_visits' => $totalViews,
            ];
        } catch (Exception $e) {
            Log::warning("GA4 fetch error: " . $e->getMessage());
            return ['connected' => false, 'daily_data' => [], 'total_users' => 0, 'total_visits' => 0];
        }
    }
}


