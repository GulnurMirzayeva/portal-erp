<?php

namespace App\Http\Controllers;

use App\Services\PortalWebsiteService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\StreamedResponse;

class AccountingController extends Controller
{
    /**
     * 32 Sütunlu Mühasibatlıq Hesabat Cədvəli və Statistikalar.
     */
    public function index(Request $request, PortalWebsiteService $portalService)
    {
        $filterMonth = $request->query('month', Carbon::now()->format('Y-m'));
        $filterBranch = $request->query('branch_id');

        $result = $portalService->getAccountingReport([
            'month' => $filterMonth,
            'branch_id' => $filterBranch,
        ]);

        return view('accounting.index', [
            'sales' => $result['sales'],
            'summary' => $result['summary'],
            'branches' => $result['branches'],
            'filterMonth' => $filterMonth,
            'filterBranch' => $filterBranch,
            'connected' => $result['connected'] ?? false,
            'error' => $result['error'] ?? null,
        ]);
    }

    /**
     * 32 Sütunlu Cədvəlin Excel/CSV formatında ixracı.
     */
    public function export(Request $request, PortalWebsiteService $portalService): StreamedResponse
    {
        $filterMonth = $request->query('month', Carbon::now()->format('Y-m'));
        $filterBranch = $request->query('branch_id');

        $result = $portalService->getAccountingReport([
            'month' => $filterMonth,
            'branch_id' => $filterBranch,
        ]);

        $sales = $result['sales'] ?? [];
        $fileName = 'muhasibatliq_32_sutun_' . $filterMonth . '.csv';

        $headers = [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => "attachment; filename=\"{$fileName}\"",
        ];

        return response()->stream(function () use ($sales) {
            $handle = fopen('php://output', 'w');

            // UTF-8 BOM Excel üçün
            fprintf($handle, chr(0xEF) . chr(0xBB) . chr(0xBF));

            // Başlıqlar
            fputcsv($handle, [
                '№',
                'Tarix',
                'Oyun',
                'Saat',
                'Say',
                'Qiymət (1 nəfər)',
                'Nəğd məbləğ',
                'Post terminal',
                'Qeyd / Endirim',
                'Resepşn',
                'Sayı',
                'Bonus 00:00',
                'Hosstes',
                'Sayı',
                'Bonus 00:00',
                'Aktyor 1',
                'Sayı',
                'Bonus 00:00',
                'Aktyor 2',
                'Sayı',
                'Bonus 00:00',
                'Operator',
                'Sayı',
                'Bonus 00:00',
                'PlusBir Aktyor',
                'Sayı',
                'PlusBir Resepşn',
                'Sayı',
                'Müştəri adı',
                'Telefon nömrəsi',
                'Qeyd',
                'Ümumi gəlir',
            ], ';');

            foreach ($sales as $row) {
                fputcsv($handle, [
                    $row['row_number'] ?? '',
                    $row['date'] ?? '',
                    $row['game_name'] ?? '',
                    $row['time'] ?? '',
                    $row['player_count'] ?? '',
                    $row['price_per_person'] ? number_format($row['price_per_person'], 2) : '',
                    $row['cash_amount'] > 0 ? number_format($row['cash_amount'], 2) : '',
                    $row['card_amount'] > 0 ? number_format($row['card_amount'], 2) : '',
                    $row['discount_note'] ?? '',
                    $row['receptionist_name'] ?? '',
                    $row['receptionist_count'] ?? '',
                    $row['receptionist_bonus_00'] ?? '',
                    $row['hostess_name'] ?? '',
                    $row['hostess_count'] ?? '',
                    $row['hostess_bonus_00'] ?? '',
                    $row['actor1_name'] ?? '',
                    $row['actor1_count'] ?? '',
                    $row['actor1_bonus_00'] ?? '',
                    $row['actor2_name'] ?? '',
                    $row['actor2_count'] ?? '',
                    $row['actor2_bonus_00'] ?? '',
                    $row['operator_name'] ?? '',
                    $row['operator_count'] ?? '',
                    $row['operator_bonus_00'] ?? '',
                    $row['plus_one_actor'] ?? '',
                    $row['plus_one_actor_count'] ?? '',
                    $row['plus_one_receptionist'] ?? '',
                    $row['plus_one_receptionist_count'] ?? '',
                    $row['customer_name'] ?? '',
                    $row['customer_phone'] ?? '',
                    $row['note'] ?? '',
                    $row['total_price'] ? number_format($row['total_price'], 2) : '',
                ], ';');
            }

            fclose($handle);
        }, 200, $headers);
    }
}
