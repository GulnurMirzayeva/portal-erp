<?php

namespace App\Services;

use Exception;
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
}
