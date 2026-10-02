<?php

namespace App\Http\Controllers;

use App\Services\PortalWebsiteService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Symfony\Component\HttpFoundation\StreamedResponse;

class AccountingController extends Controller
{
    /**
     * Elektron Qaimələr - Filiallar üzrə ayrılmış mühasibatlıq hesabatı və Excel cədvəli.
     */
    public function index(Request $request, PortalWebsiteService $portalService)
    {
        $filterMonth = $request->query('month', Carbon::now()->format('Y-m'));
        $filterBranch = $request->query('branch_id');

        // Bütün filialları əldə edirik
        $branches = $portalService->getBranches();

        // Əgər filial seçilməyibsə, avtomatik olaraq ilk mövcud filialı seçirik
        if ($filterBranch === null && !empty($branches)) {
            $filterBranch = $branches[0]['id'];
        }

        $apiBranchId = ($filterBranch === 'all') ? null : $filterBranch;

        $result = $portalService->getAccountingReport([
            'month' => $filterMonth,
            'branch_id' => $apiBranchId,
        ]);

        if (empty($branches) && !empty($result['branches'])) {
            $branches = $result['branches'];
        }

        // Seçilmiş filialın adını müəyyən edirik
        $selectedBranchName = 'Bütün Filiallar';
        if ($filterBranch && $filterBranch !== 'all') {
            foreach ($branches as $b) {
                if ($b['id'] == $filterBranch) {
                    $selectedBranchName = $b['name'];
                    break;
                }
            }
        }

        return view('accounting.index', [
            'sales' => $result['sales'] ?? [],
            'summary' => $result['summary'] ?? [],
            'branches' => $branches,
            'filterMonth' => $filterMonth,
            'filterBranch' => $filterBranch,
            'selectedBranchName' => $selectedBranchName,
            'connected' => $result['connected'] ?? false,
            'error' => $result['error'] ?? null,
        ]);
    }

    /**
     * Seçilmiş filialın satış datasının Excel (.xls) və ya CSV formatında ixracı.
     */
    public function export(Request $request, PortalWebsiteService $portalService): StreamedResponse
    {
        $filterMonth = $request->query('month', Carbon::now()->format('Y-m'));
        $filterBranch = $request->query('branch_id');
        $format = $request->query('format', 'excel'); // 'excel' (XLS) və ya 'csv'

        $branches = $portalService->getBranches();
        if ($filterBranch === null && !empty($branches)) {
            $filterBranch = $branches[0]['id'];
        }

        $apiBranchId = ($filterBranch === 'all') ? null : $filterBranch;

        $result = $portalService->getAccountingReport([
            'month' => $filterMonth,
            'branch_id' => $apiBranchId,
        ]);

        $sales = $result['sales'] ?? [];

        // Filial adını müəyyən edirik
        $branchName = 'Butun_Filiallar';
        $branchLabel = 'Bütün Filiallar';
        if ($filterBranch && $filterBranch !== 'all') {
            foreach ($branches as $b) {
                if ($b['id'] == $filterBranch) {
                    $branchLabel = $b['name'];
                    $branchName = Str::slug($b['name'], '_');
                    break;
                }
            }
        }

        $nowStr = Carbon::now()->format('d.m.Y H:i');

        if ($format === 'csv') {
            $fileName = "elektron_qaime_{$branchName}_{$filterMonth}.csv";
            $headers = [
                'Content-Type' => 'text/csv; charset=UTF-8',
                'Content-Disposition' => "attachment; filename=\"{$fileName}\"",
            ];

            return response()->stream(function () use ($sales) {
                $handle = fopen('php://output', 'w');
                fprintf($handle, chr(0xEF) . chr(0xBB) . chr(0xBF)); // UTF-8 BOM

                fputcsv($handle, [
                    '№', 'Tarix', 'Oyun', 'Filial', 'Saat', 'Say', 'Qiymət', 'Nağd', 'Terminal',
                    'Qeyd/Endirim', 'Resepşn', 'Say', 'Bonus 00:00', 'Hostes', 'Say', 'Bonus 00:00',
                    'Aktyor 1', 'Say', 'Bonus 00:00', 'Aktyor 2', 'Say', 'Bonus 00:00',
                    'Operator', 'Say', 'Bonus 00:00', 'PlusBir Aktyor', 'Say', 'PlusBir Resepşn',
                    'Say', 'Müştəri', 'Telefon', 'Qeyd', 'Ümumi Gəlir'
                ], ';');

                foreach ($sales as $row) {
                    fputcsv($handle, [
                        $row['row_number'] ?? '',
                        $row['date'] ?? '',
                        $row['game_name'] ?? '',
                        $row['branch_name'] ?? '',
                        $row['time'] ?? '',
                        $row['player_count'] ?? '',
                        $row['price_per_person'] ? number_format($row['price_per_person'], 2) : '',
                        $row['cash_amount'] > 0 ? number_format($row['cash_amount'], 2) : '0.00',
                        $row['card_amount'] > 0 ? number_format($row['card_amount'], 2) : '0.00',
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
                        $row['total_price'] ? number_format($row['total_price'], 2) : '0.00',
                    ], ';');
                }

                fclose($handle);
            }, 200, $headers);
        }

        // Default: Native Excel format (.xls) with clean styles, formulas, and totals
        $fileName = "elektron_qaime_{$branchName}_{$filterMonth}.xls";
        $headers = [
            'Content-Type' => 'application/vnd.ms-excel; charset=UTF-8',
            'Content-Disposition' => "attachment; filename=\"{$fileName}\"",
            'Pragma' => 'no-cache',
            'Expires' => '0',
        ];

        return response()->stream(function () use ($sales, $branchLabel, $filterMonth, $nowStr) {
            $totalPlayers = 0;
            $totalCash = 0;
            $totalCard = 0;
            $totalRev = 0;

            foreach ($sales as $r) {
                $totalPlayers += (int)($r['player_count'] ?? 0);
                $totalCash += (float)($r['cash_amount'] ?? 0);
                $totalCard += (float)($r['card_amount'] ?? 0);
                $totalRev += (float)($r['total_price'] ?? 0);
            }

            echo '<html xmlns:o="urn:schemas-microsoft-com:office:office" xmlns:x="urn:schemas-microsoft-com:office:excel" xmlns="http://www.w3.org/TR/REC-html40">';
            echo '<head><meta http-equiv="Content-Type" content="text/html; charset=UTF-8">';
            echo '<!--[if gte mso 9]><xml><x:ExcelWorkbook><x:ExcelWorksheets><x:ExcelWorksheet><x:Name>Elektron Qaimə</x:Name><x:WorksheetOptions><x:DisplayGridlines/></x:WorksheetOptions></x:ExcelWorksheet></x:ExcelWorksheets></x:ExcelWorkbook></xml><![endif]-->';
            echo '<style>
                table { border-collapse: collapse; width: 100%; font-family: "Calibri", "Arial", sans-serif; font-size: 11pt; }
                th { background-color: #2a3042; color: #ffffff; border: 1px solid #363d54; padding: 8px 10px; font-weight: bold; text-align: center; }
                td { border: 1px solid #dcdcdc; padding: 6px 8px; vertical-align: middle; }
                .title-row { font-size: 16pt; font-weight: bold; color: #2a3042; height: 35px; }
                .meta-row { font-size: 11pt; color: #555555; background-color: #f8f9fa; }
                .sec-header { background-color: #4b546a; color: #ffffff; font-size: 10pt; font-weight: bold; }
                .total-row { background-color: #eaf2ff; font-weight: bold; border-top: 2px solid #556ee6; }
                .text-center { text-align: center; }
                .text-right { text-align: right; }
                .num { mso-number-format: "\#\,\#\#0\.00"; text-align: right; }
            </style>';
            echo '</head><body>';
            echo '<table>';

            // Başlıq sətirləri
            echo '<tr><td colspan="32" class="title-row">PORTAL ENTERTAINMENT — ELEKTRON SATIŞ QAİMƏSİ</td></tr>';
            echo '<tr><td colspan="32" class="meta-row"><strong>Filial:</strong> ' . htmlspecialchars($branchLabel) . ' | <strong>Hesabat Dövrü:</strong> ' . htmlspecialchars($filterMonth) . ' | <strong>Sənəd №:</strong> EQ-' . htmlspecialchars($filterMonth) . ' | <strong>İxrac Tarixi:</strong> ' . $nowStr . '</td></tr>';
            echo '<tr><td colspan="32" style="height: 10px; border: none;"></td></tr>';

            // Qruplaşdırılmış Cədvəl Başlıqları
            echo '<tr>';
            echo '<th colspan="9" style="background-color: #556ee6;">SATIŞ MƏLUMATLARI</th>';
            echo '<th colspan="3" style="background-color: #6c757d;">RESEPŞN</th>';
            echo '<th colspan="3" style="background-color: #343a40;">HOSTES</th>';
            echo '<th colspan="3" style="background-color: #17a2b8;">AKTYOR 1</th>';
            echo '<th colspan="3" style="background-color: #17a2b8;">AKTYOR 2</th>';
            echo '<th colspan="3" style="background-color: #6c757d;">OPERATOR</th>';
            echo '<th colspan="2" style="background-color: #ffc107; color: #000000;">PLUS-BİR AKTYOR</th>';
            echo '<th colspan="2" style="background-color: #ffc107; color: #000000;">PLUS-BİR RESEPŞN</th>';
            echo '<th colspan="4" style="background-color: #28a745;">MÜŞTƏRİ VƏ YEKUN</th>';
            echo '</tr>';

            // Sütun Başlıqları
            echo '<tr>';
            echo '<th>№</th><th>Tarix</th><th>Oyun</th><th>Saat</th><th>Say</th><th>Qiymət</th><th>Nağd</th><th>Terminal</th><th>Qeyd/Endirim</th>';
            echo '<th>İşçi</th><th>Say</th><th>00:00</th>';
            echo '<th>İşçi</th><th>Say</th><th>00:00</th>';
            echo '<th>İşçi</th><th>Say</th><th>00:00</th>';
            echo '<th>İşçi</th><th>Say</th><th>00:00</th>';
            echo '<th>İşçi</th><th>Say</th><th>00:00</th>';
            echo '<th>İşçi</th><th>Say</th>';
            echo '<th>İşçi</th><th>Say</th>';
            echo '<th>Müştəri</th><th>Telefon</th><th>Rəy/Qeyd</th><th style="background-color: #28a745;">Ümumi Gəlir (₼)</th>';
            echo '</tr>';

            // Məlumat Sətirləri
            foreach ($sales as $row) {
                echo '<tr>';
                echo '<td class="text-center">' . htmlspecialchars($row['row_number'] ?? '') . '</td>';
                echo '<td class="text-center">' . htmlspecialchars($row['date'] ?? '') . '</td>';
                echo '<td><strong>' . htmlspecialchars($row['game_name'] ?? '') . '</strong></td>';
                echo '<td class="text-center">' . htmlspecialchars($row['time'] ?? '') . '</td>';
                echo '<td class="text-center">' . htmlspecialchars($row['player_count'] ?? '') . '</td>';
                echo '<td class="num">' . number_format((float)($row['price_per_person'] ?? 0), 2) . '</td>';
                echo '<td class="num">' . number_format((float)($row['cash_amount'] ?? 0), 2) . '</td>';
                echo '<td class="num">' . number_format((float)($row['card_amount'] ?? 0), 2) . '</td>';
                echo '<td>' . htmlspecialchars($row['discount_note'] ?? '') . '</td>';

                echo '<td>' . htmlspecialchars($row['receptionist_name'] ?? '—') . '</td>';
                echo '<td class="text-center">' . htmlspecialchars($row['receptionist_count'] ?? '') . '</td>';
                echo '<td class="text-center">' . ($row['receptionist_bonus_00'] ? '1' : '—') . '</td>';

                echo '<td>' . htmlspecialchars($row['hostess_name'] ?? '—') . '</td>';
                echo '<td class="text-center">' . htmlspecialchars($row['hostess_count'] ?? '') . '</td>';
                echo '<td class="text-center">' . ($row['hostess_bonus_00'] ? '1' : '—') . '</td>';

                echo '<td>' . htmlspecialchars($row['actor1_name'] ?? '—') . '</td>';
                echo '<td class="text-center">' . htmlspecialchars($row['actor1_count'] ?? '') . '</td>';
                echo '<td class="text-center">' . ($row['actor1_bonus_00'] ? '1' : '—') . '</td>';

                echo '<td>' . htmlspecialchars($row['actor2_name'] ?? '—') . '</td>';
                echo '<td class="text-center">' . htmlspecialchars($row['actor2_count'] ?? '') . '</td>';
                echo '<td class="text-center">' . ($row['actor2_bonus_00'] ? '1' : '—') . '</td>';

                echo '<td>' . htmlspecialchars($row['operator_name'] ?? '—') . '</td>';
                echo '<td class="text-center">' . htmlspecialchars($row['operator_count'] ?? '') . '</td>';
                echo '<td class="text-center">' . ($row['operator_bonus_00'] ? '1' : '—') . '</td>';

                echo '<td>' . htmlspecialchars($row['plus_one_actor'] ?? '—') . '</td>';
                echo '<td class="text-center">' . htmlspecialchars($row['plus_one_actor_count'] ?? '') . '</td>';

                echo '<td>' . htmlspecialchars($row['plus_one_receptionist'] ?? '—') . '</td>';
                echo '<td class="text-center">' . htmlspecialchars($row['plus_one_receptionist_count'] ?? '') . '</td>';

                echo '<td>' . htmlspecialchars($row['customer_name'] ?? '') . '</td>';
                echo '<td>' . htmlspecialchars($row['customer_phone'] ?? '') . '</td>';
                echo '<td>' . htmlspecialchars($row['note'] ?? '') . '</td>';
                echo '<td class="num" style="font-weight: bold; background-color: #f0fff4;">' . number_format((float)($row['total_price'] ?? 0), 2) . '</td>';
                echo '</tr>';
            }

            // Yekun Cəmi Sətiri
            echo '<tr class="total-row">';
            echo '<td colspan="4" class="text-center"><strong>YEKUN CƏMİ (' . count($sales) . ' Satış)</strong></td>';
            echo '<td class="text-center"><strong>' . $totalPlayers . '</strong></td>';
            echo '<td></td>';
            echo '<td class="num"><strong>' . number_format($totalCash, 2) . '</strong></td>';
            echo '<td class="num"><strong>' . number_format($totalCard, 2) . '</strong></td>';
            echo '<td colspan="23"></td>';
            echo '<td class="num" style="font-size: 12pt; color: #28a745;"><strong>' . number_format($totalRev, 2) . ' ₼</strong></td>';
            echo '</tr>';

            echo '</table></body></html>';
        }, 200, $headers);
    }
}

