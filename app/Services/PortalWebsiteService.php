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
        // Əgər bütün aylar/datalar istənibsə, API-yə date_from və date_to göndəririk ki, oktyabr limiti tətbiq olunmasın
        if (isset($queryParams['month']) && $queryParams['month'] === 'all') {
            unset($queryParams['month']);
            $queryParams['date_from'] = '2020-01-01';
            $queryParams['date_to'] = date('Y-12-31', strtotime('+1 year'));
        }

        try {
            $response = Http::timeout(25)
                ->withHeaders([
                    'X-ERP-API-KEY' => $this->apiToken,
                    'Accept' => 'application/json',
                ])
                ->get("{$this->baseUrl}/api/erp/accounting-report", $queryParams);

            if ($response->successful()) {
                $data = $response->json();
                return [
                    'connected' => true,
                    'summary' => $data['summary'] ?? $defaultSummary,
                    'branches' => $data['branches'] ?? [],
                    'sales' => $data['sales'] ?? [],
                ];
            }

            return [
                'connected' => false,
                'error' => 'API Xətası (' . $response->status() . '): ' . ($response->json('message') ?? 'Sorğu uğursuz oldu.'),
                'summary' => $defaultSummary,
                'branches' => [],
                'sales' => [],
            ];
        } catch (Exception $e) {
            Log::warning("PortalWebsite API connection error: " . $e->getMessage());

            return [
                'connected' => false,
                'error' => "PortalWebsite serverinə qoşulmaq mümkün olmadı ({$this->baseUrl}). Serverin işlədiyindən əmin olun.",
                'summary' => $defaultSummary,
                'branches' => [],
                'sales' => [],
            ];
        }
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
                return [
                    'success' => true,
                    'message' => $response->json('message', 'Xərclər uğurla yadda saxlanıldı.'),
                    'expenses' => $response->json('expenses', $expenses),
                    'deleted_count' => $response->json('deleted_count', count($deletedIds)),
                ];
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
}

