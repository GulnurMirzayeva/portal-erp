<?php

namespace App\Http\Controllers;

use App\Models\AccountingRecord;
use App\Services\PortalWebsiteService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;
use Symfony\Component\HttpFoundation\StreamedResponse;

class AccountingController extends Controller
{
    /**
     * Filiallar üzrə ayrılmış elektron qaimələr cədvəli (Excel interfeysi).
     */
    public function index(Request $request, PortalWebsiteService $portalService)
    {
        // Əgər URL-də 'month=all' gələrsə, cari aya yönləndiririk
        if ($request->query('month') === 'all') {
            return redirect()->route('accounting.index', array_filter([
                'branch_id' => $request->query('branch_id'),
                'month' => Carbon::now()->format('Y-m'),
            ]));
        }

        $availableMonths = $this->getAvailableMonths();

        // Əgər istifadəçi xüsusi ay seçməyibsə və ya format düz deyilsə, cari ayı default edirik
        $filterMonth = $request->query('month');
        if (empty($filterMonth) || !preg_match('/^\d{4}-\d{2}$/', $filterMonth)) {
            $filterMonth = Carbon::now()->format('Y-m');
        }

        $filterBranch = $request->query('branch_id');

        // Bütün filialları əldə edirik və "Ofis" filialını ləğv edirik
        $allBranches = $portalService->getBranches();
        $branches = array_values(array_filter($allBranches, function ($b) {
            $name = mb_strtolower($b['name'] ?? '');
            return !str_contains($name, 'ofis') && !str_contains($name, 'office');
        }));

        // Əgər filial seçilməyibsə və ya "Ofis" seçilibsə, avtomatik olaraq ilk filialı seçirik
        if (($filterBranch === null || $filterBranch == 3) && !empty($branches)) {
            $filterBranch = $branches[0]['id'];
        }

        $apiBranchId = ($filterBranch === 'all') ? null : $filterBranch;

        // Seçilmiş filialın adını təyin edirik
        $selectedBranchName = 'Bütün Filiallar';
        if ($filterBranch && $filterBranch !== 'all') {
            foreach ($branches as $b) {
                if ($b['id'] == $filterBranch) {
                    $selectedBranchName = $b['name'];
                    break;
                }
            }
        }

        // Seçilmiş ayın Azərbaycan dilində adını təyin edirik
        $selectedMonthName = '';
        foreach ($availableMonths as $m) {
            if ($m['key'] === $filterMonth) {
                $selectedMonthName = $m['name'];
                break;
            }
        }
        if (!$selectedMonthName) {
            $monthNamesAz = [
                '01' => 'Yanvar', '02' => 'Fevral', '03' => 'Mart', '04' => 'Aprel',
                '05' => 'May', '06' => 'İyun', '07' => 'İyul', '08' => 'Avqust',
                '09' => 'Sentyabr', '10' => 'Oktyabr', '11' => 'Noyabr', '12' => 'Dekabr',
            ];
            $parts = explode('-', $filterMonth);
            $selectedMonthName = ($monthNamesAz[$parts[1] ?? ''] ?? ($parts[1] ?? '')) . ' ' . ($parts[0] ?? '');
        }

        // Əvvəlcə yerli bazada mühasibin redaktə edib yadda saxladığı qeyd varmı yoxlayırıq
        $savedRecord = null;
        if ($apiBranchId !== null) {
            $savedRecord = AccountingRecord::where('branch_id', (string)$apiBranchId)
                ->where('month', $filterMonth)
                ->first();
        }

        if ($savedRecord && !empty($savedRecord->sales_data)) {
            $sales = $savedRecord->sales_data;
            $summary = $savedRecord->summary_data ?? $this->calculateSummary($sales);
            $connected = true;
            $error = null;
            $hasCustomEdits = true;
            $lastSavedAt = $savedRecord->updated_at ? $savedRecord->updated_at->format('d.m.Y H:i') : null;
        } else {
            // Əgər yadda saxlanmış qeyd yoxdursa, birbaşa PortalWebsite API-dən çəkirik
            $result = $portalService->getAccountingReport([
                'month' => $filterMonth,
                'branch_id' => $apiBranchId,
            ]);

            $sales = $result['sales'] ?? [];
            $summary = $result['summary'] ?? $this->calculateSummary($sales);
            $connected = $result['connected'] ?? false;
            $error = $result['error'] ?? null;
            $hasCustomEdits = false;
            $lastSavedAt = null;
        }

        return view('accounting.index', [
            'sales' => $sales,
            'summary' => $summary,
            'branches' => $branches,
            'availableMonths' => $availableMonths,
            'selectedMonthName' => $selectedMonthName,
            'filterMonth' => $filterMonth,
            'filterBranch' => $filterBranch,
            'selectedBranchName' => $selectedBranchName,
            'connected' => $connected,
            'error' => $error,
            'hasCustomEdits' => $hasCustomEdits,
            'lastSavedAt' => $lastSavedAt,
        ]);
    }

    /**
     * Mühasibin Excel xanalarında etdiyi dəyişiklikləri həqiqi verilənlər bazasında (DB) yeniləyir.
     */
    public function save(Request $request)
    {
        $request->validate([
            'branch_id' => 'required',
            'month' => 'required|string',
            'sales' => 'required|array',
        ]);

        $branchId = (string)$request->input('branch_id');
        $month = $request->input('month');
        $sales = $request->input('sales');
        $summary = $this->calculateSummary($sales);

        // 1. portalGamesWebsite bazasında real cədvəli (sales və reservations) həqiqətən UPDATE edirik
        $updatedDbCount = 0;
        try {
            foreach ($sales as $row) {
                $saleId = $row['id'] ?? null;
                if ($saleId && is_numeric($saleId)) {
                    $updateFields = [];

                    // Tarix
                    if (!empty($row['date'])) {
                        try {
                            $updateFields['reservation_date'] = Carbon::parse($row['date'])->format('Y-m-d');
                        } catch (\Exception $e) {}
                    }

                    // Saat
                    if (!empty($row['time'])) {
                        try {
                            $updateFields['reservation_time'] = Carbon::parse($row['time'])->format('H:i:s');
                        } catch (\Exception $e) {}
                    }

                    // Say və Qiymət
                    if (isset($row['player_count'])) {
                        $updateFields['player_count'] = (int)$row['player_count'];
                    }
                    if (isset($row['price_per_person'])) {
                        $updateFields['price_per_person'] = (float)$row['price_per_person'];
                    }

                    // Nağd və Terminal (Kart) ödənişləri və Yekun Məbləğ
                    $cash = isset($row['cash_amount']) ? (float)$row['cash_amount'] : 0.0;
                    $card = isset($row['card_amount']) ? (float)$row['card_amount'] : 0.0;
                    $totalPrice = isset($row['total_price']) ? (float)$row['total_price'] : ($cash + $card);

                    $updateFields['total_price'] = $totalPrice;
                    $updateFields['payments'] = json_encode([
                        ['type' => 'cash', 'amount' => number_format($cash, 2, '.', '')],
                        ['type' => 'card', 'amount' => number_format($card, 2, '.', '')],
                    ]);

                    // Qeyd və Endirim
                    if (isset($row['note'])) {
                        $updateFields['note'] = (string)$row['note'];
                    }
                    if (isset($row['discount_note'])) {
                        $updateFields['discount_type'] = (string)$row['discount_note'];
                    }

                    // Müştəri adı
                    if (isset($row['customer_name'])) {
                        $updateFields['group_leader_name'] = (string)$row['customer_name'];
                    }

                    $updateFields['updated_at'] = Carbon::now();

                    // Real portalGamesWebsite.sales cədvəlini UPDATE edirik
                    $affected = DB::connection('portal_website')->table('sales')
                        ->where('id', $saleId)
                        ->update($updateFields);

                    if ($affected) {
                        $updatedDbCount++;
                    }

                    // Müştəri nömrəsi varsa, əlaqəli rezervasiyanı da yeniləyirik
                    if (!empty($row['customer_phone'])) {
                        $saleRecord = DB::connection('portal_website')->table('sales')
                            ->where('id', $saleId)
                            ->first(['reservation_id']);
                        if ($saleRecord && $saleRecord->reservation_id) {
                            DB::connection('portal_website')->table('reservations')
                                ->where('id', $saleRecord->reservation_id)
                                ->update(['user_phone' => (string)$row['customer_phone']]);
                        }
                    }
                }
            }
        } catch (\Exception $e) {
            Log::error("Real database update error: " . $e->getMessage());
        }

        // 2. ERP sisteminin özündə də AccountingRecord-u yeniləyirik
        $record = AccountingRecord::updateOrCreate(
            [
                'branch_id' => $branchId,
                'month' => $month,
            ],
            [
                'sales_data' => $sales,
                'summary_data' => $summary,
                'updated_by' => auth()->id(),
            ]
        );

        return response()->json([
            'success' => true,
            'message' => "Məlumatlar bazada (DB) və sistemdə həqiqətən yeniləndi!",
            'saved_at' => $record->updated_at->format('d.m.Y H:i'),
            'summary' => $summary,
            'db_updated' => $updatedDbCount,
        ]);
    }

    /**
     * Filialın qaimə məlumatlarını ilkin API vəziyyətinə qaytarır.
     */
    public function reset(Request $request)
    {
        $branchId = (string)$request->input('branch_id');
        $month = $request->input('month');

        if ($branchId && $month) {
            AccountingRecord::where('branch_id', $branchId)
                ->where('month', $month)
                ->delete();
        }

        return redirect()->route('accounting.index', [
            'branch_id' => $branchId,
            'month' => $month,
        ])->with('success', 'Məlumatlar ilkin sayt göstəricilərinə bərpa olundu.');
    }

    /**
     * Filialın qaiməsini peşəkar Microsoft Excel (.xlsx) formatında ixrac edir.
     */
    public function export(Request $request, PortalWebsiteService $portalService): StreamedResponse
    {
        $filterMonth = $request->query('month', Carbon::now()->format('Y-m'));
        $filterBranch = $request->query('branch_id');
        $format = $request->query('format', 'excel'); // 'excel' (.xlsx) və ya 'csv'

        $allBranches = $portalService->getBranches();
        $branches = array_values(array_filter($allBranches, function ($b) {
            $name = mb_strtolower($b['name'] ?? '');
            return !str_contains($name, 'ofis') && !str_contains($name, 'office');
        }));

        if (($filterBranch === null || $filterBranch == 3) && !empty($branches)) {
            $filterBranch = $branches[0]['id'];
        }

        $apiBranchId = ($filterBranch === 'all') ? null : $filterBranch;

        $branchLabel = 'Bütün Filiallar';
        $branchSlug = 'Butun_Filiallar';
        if ($filterBranch && $filterBranch !== 'all') {
            foreach ($branches as $b) {
                if ($b['id'] == $filterBranch) {
                    $branchLabel = $b['name'];
                    $branchSlug = Str::slug($b['name'], '_');
                    break;
                }
            }
        }

        // Əvvəlcə yerli redaktə olunmuş qeydə baxırıq
        $savedRecord = null;
        if ($apiBranchId !== null) {
            $savedRecord = AccountingRecord::where('branch_id', (string)$apiBranchId)
                ->where('month', $filterMonth)
                ->first();
        }

        if ($savedRecord && !empty($savedRecord->sales_data)) {
            $sales = $savedRecord->sales_data;
        } else {
            $result = $portalService->getAccountingReport([
                'month' => $filterMonth,
                'branch_id' => $apiBranchId,
            ]);
            $sales = $result['sales'] ?? [];
        }

        // CSV ixrac
        if ($format === 'csv') {
            $fileName = "elektron_qaime_{$branchSlug}_{$filterMonth}.csv";
            return response()->stream(function () use ($sales) {
                $handle = fopen('php://output', 'w');
                fprintf($handle, chr(0xEF) . chr(0xBB) . chr(0xBF));
                fputcsv($handle, [
                    '№', 'Tarix', 'Oyun', 'Saat', 'Say', 'Qiymət', 'Nağd', 'Terminal', 'Qeyd / Endirim',
                    'Resepşn', 'Say', '00:00', 'Hostes', 'Say', '00:00', 'Aktyor 1', 'Say', '00:00',
                    'Aktyor 2', 'Say', '00:00', 'Operator', 'Say', '00:00', 'PlusBir Aktyor', 'Say',
                    'PlusBir Resepşn', 'Say', 'Müştəri', 'Telefon', 'Qeyd', 'Ümumi Gəlir'
                ], ';');

                foreach ($sales as $r) {
                    fputcsv($handle, [
                        $r['row_number'] ?? '',
                        $r['date'] ?? '',
                        $r['game_name'] ?? '',
                        $r['time'] ?? '',
                        $r['player_count'] ?? '',
                        $r['price_per_person'] ?? '',
                        $r['cash_amount'] ?? '0.00',
                        $r['card_amount'] ?? '0.00',
                        $r['discount_note'] ?? '',
                        $r['receptionist_name'] ?? '',
                        $r['receptionist_count'] ?? '',
                        $r['receptionist_bonus_00'] ?? '',
                        $r['hostess_name'] ?? '',
                        $r['hostess_count'] ?? '',
                        $r['hostess_bonus_00'] ?? '',
                        $r['actor1_name'] ?? '',
                        $r['actor1_count'] ?? '',
                        $r['actor1_bonus_00'] ?? '',
                        $r['actor2_name'] ?? '',
                        $r['actor2_count'] ?? '',
                        $r['actor2_bonus_00'] ?? '',
                        $r['operator_name'] ?? '',
                        $r['operator_count'] ?? '',
                        $r['operator_bonus_00'] ?? '',
                        $r['plus_one_actor'] ?? '',
                        $r['plus_one_actor_count'] ?? '',
                        $r['plus_one_receptionist'] ?? '',
                        $r['plus_one_receptionist_count'] ?? '',
                        $r['customer_name'] ?? '',
                        $r['customer_phone'] ?? '',
                        $r['note'] ?? '',
                        $r['total_price'] ?? '0.00',
                    ], ';');
                }
                fclose($handle);
            }, 200, [
                'Content-Type' => 'text/csv; charset=UTF-8',
                'Content-Disposition' => "attachment; filename=\"{$fileName}\"",
            ]);
        }

        // Standart Microsoft Excel (.xlsx) generasiyası
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle(mb_substr($branchLabel, 0, 31));

        // Grid lines aktiv edirik
        $sheet->setShowGridLines(true);

        // 1. Sənəd Başlığı
        $sheet->mergeCells('A1:AF1');
        $sheet->setCellValue('A1', 'PORTAL GAMES — ELEKTRON SATIŞ QAİMƏSİ');
        $sheet->getStyle('A1')->getFont()->setSize(16)->setBold(true)->getColor()->setRGB('FFFFFF');
        $sheet->getStyle('A1')->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setRGB('107C41'); // Excel Green
        $sheet->getStyle('A1')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER)->setVertical(Alignment::VERTICAL_CENTER);
        $sheet->getRowDimension(1)->setRowHeight(36);

        // 2. Filial & Tarix Məlumatları
        $sheet->mergeCells('A2:AF2');
        $periodName = Carbon::parse($filterMonth . '-01')->translatedFormat('F Y');
        $sheet->setCellValue('A2', "Filial: {$branchLabel}   |   Hesabat Dövrü: {$periodName}   |   Çıxarış Tarixi: " . Carbon::now()->format('d.m.Y H:i'));
        $sheet->getStyle('A2')->getFont()->setSize(11)->setItalic(true)->getColor()->setRGB('333333');
        $sheet->getStyle('A2')->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setRGB('E8F5E9');
        $sheet->getStyle('A2')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER)->setVertical(Alignment::VERTICAL_CENTER);
        $sheet->getRowDimension(2)->setRowHeight(24);

        // 3. Kateqoriya Başlıqları (Sətir 3)
        $sheet->mergeCells('A3:I3');
        $sheet->setCellValue('A3', 'SATIŞ MƏLUMATLARI');
        $sheet->getStyle('A3')->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setRGB('107C41');
        $sheet->getStyle('A3')->getFont()->setBold(true)->getColor()->setRGB('FFFFFF');

        $sheet->mergeCells('J3:L3');
        $sheet->setCellValue('J3', 'RESEPŞN');
        $sheet->getStyle('J3')->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setRGB('4B546A');
        $sheet->getStyle('J3')->getFont()->setBold(true)->getColor()->setRGB('FFFFFF');

        $sheet->mergeCells('M3:O3');
        $sheet->setCellValue('M3', 'HOSTES');
        $sheet->getStyle('M3')->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setRGB('343A40');
        $sheet->getStyle('M3')->getFont()->setBold(true)->getColor()->setRGB('FFFFFF');

        $sheet->mergeCells('P3:R3');
        $sheet->setCellValue('P3', 'AKTYOR 1');
        $sheet->getStyle('P3')->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setRGB('17A2B8');
        $sheet->getStyle('P3')->getFont()->setBold(true)->getColor()->setRGB('FFFFFF');

        $sheet->mergeCells('S3:U3');
        $sheet->setCellValue('S3', 'AKTYOR 2');
        $sheet->getStyle('S3')->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setRGB('138496');
        $sheet->getStyle('S3')->getFont()->setBold(true)->getColor()->setRGB('FFFFFF');

        $sheet->mergeCells('V3:X3');
        $sheet->setCellValue('V3', 'OPERATOR');
        $sheet->getStyle('V3')->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setRGB('4B546A');
        $sheet->getStyle('V3')->getFont()->setBold(true)->getColor()->setRGB('FFFFFF');

        $sheet->mergeCells('Y3:Z3');
        $sheet->setCellValue('Y3', 'PLUS-BİR AKTYOR');
        $sheet->getStyle('Y3')->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setRGB('D39E00');
        $sheet->getStyle('Y3')->getFont()->setBold(true)->getColor()->setRGB('000000');

        $sheet->mergeCells('AA3:AB3');
        $sheet->setCellValue('AA3', 'PLUS-BİR RESEPŞN');
        $sheet->getStyle('AA3')->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setRGB('D39E00');
        $sheet->getStyle('AA3')->getFont()->setBold(true)->getColor()->setRGB('000000');

        $sheet->mergeCells('AC3:AF3');
        $sheet->setCellValue('AC3', 'MÜŞTƏRİ VƏ YEKUN');
        $sheet->getStyle('AC3')->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setRGB('1E7E34');
        $sheet->getStyle('AC3')->getFont()->setBold(true)->getColor()->setRGB('FFFFFF');

        $sheet->getStyle('A3:AF3')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER)->setVertical(Alignment::VERTICAL_CENTER);
        $sheet->getRowDimension(3)->setRowHeight(22);

        // 4. Sütun Başlıqları (Sətir 4)
        $columns = [
            '№', 'Tarix', 'Oyun', 'Saat', 'Say', 'Qiymət', 'Nağd', 'Terminal', 'Qeyd / Endirim',
            'İşçi', 'Sayı', '00:00',
            'İşçi', 'Sayı', '00:00',
            'İşçi', 'Sayı', '00:00',
            'İşçi', 'Sayı', '00:00',
            'İşçi', 'Sayı', '00:00',
            'İşçi', 'Sayı',
            'İşçi', 'Sayı',
            'Müştəri', 'Telefon', 'Rəy / Qeyd', 'Ümumi Gəlir'
        ];

        $colIndex = 1;
        foreach ($columns as $colTitle) {
            $sheet->setCellValue([$colIndex, 4], $colTitle);
            $colIndex++;
        }

        $sheet->getStyle('A4:AF4')->getFont()->setBold(true)->setSize(10);
        $sheet->getStyle('A4:AF4')->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setRGB('F1F3F5');
        $sheet->getStyle('A4:AF4')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER)->setVertical(Alignment::VERTICAL_CENTER);
        $sheet->getRowDimension(4)->setRowHeight(26);

        // 5. Məlumat Sətirləri
        $rowNum = 5;
        foreach ($sales as $r) {
            $cash = (float)($r['cash_amount'] ?? 0);
            $card = (float)($r['card_amount'] ?? 0);
            $price = (float)($r['price_per_person'] ?? 0);
            $total = (float)($r['total_price'] ?? ($cash + $card));

            $sheet->setCellValue('A' . $rowNum, $r['row_number'] ?? ($rowNum - 4));
            $sheet->setCellValue('B' . $rowNum, $r['date'] ?? '');
            $sheet->setCellValue('C' . $rowNum, $r['game_name'] ?? '');
            $sheet->setCellValue('D' . $rowNum, $r['time'] ?? '');
            $sheet->setCellValue('E' . $rowNum, (int)($r['player_count'] ?? 0));
            $sheet->setCellValue('F' . $rowNum, $price);
            $sheet->setCellValue('G' . $rowNum, $cash);
            $sheet->setCellValue('H' . $rowNum, $card);
            $sheet->setCellValue('I' . $rowNum, $r['discount_note'] ?? '');

            // Resepşn
            $sheet->setCellValue('J' . $rowNum, $r['receptionist_name'] ?? '');
            $sheet->setCellValue('K' . $rowNum, (int)($r['receptionist_count'] ?? 0));
            $sheet->setCellValue('L' . $rowNum, !empty($r['receptionist_bonus_00']) ? 1 : 0);

            // Hostes
            $sheet->setCellValue('M' . $rowNum, $r['hostess_name'] ?? '');
            $sheet->setCellValue('N' . $rowNum, (int)($r['hostess_count'] ?? 0));
            $sheet->setCellValue('O' . $rowNum, !empty($r['hostess_bonus_00']) ? 1 : 0);

            // Aktyor 1
            $sheet->setCellValue('P' . $rowNum, $r['actor1_name'] ?? '');
            $sheet->setCellValue('Q' . $rowNum, (int)($r['actor1_count'] ?? 0));
            $sheet->setCellValue('R' . $rowNum, !empty($r['actor1_bonus_00']) ? 1 : 0);

            // Aktyor 2
            $sheet->setCellValue('S' . $rowNum, $r['actor2_name'] ?? '');
            $sheet->setCellValue('T' . $rowNum, (int)($r['actor2_count'] ?? 0));
            $sheet->setCellValue('U' . $rowNum, !empty($r['actor2_bonus_00']) ? 1 : 0);

            // Operator
            $sheet->setCellValue('V' . $rowNum, $r['operator_name'] ?? '');
            $sheet->setCellValue('W' . $rowNum, (int)($r['operator_count'] ?? 0));
            $sheet->setCellValue('X' . $rowNum, !empty($r['operator_bonus_00']) ? 1 : 0);

            // PlusBir
            $sheet->setCellValue('Y' . $rowNum, $r['plus_one_actor'] ?? '');
            $sheet->setCellValue('Z' . $rowNum, (int)($r['plus_one_actor_count'] ?? 0));
            $sheet->setCellValue('AA' . $rowNum, $r['plus_one_receptionist'] ?? '');
            $sheet->setCellValue('AB' . $rowNum, (int)($r['plus_one_receptionist_count'] ?? 0));

            // Müştəri & Yekun
            $sheet->setCellValue('AC' . $rowNum, $r['customer_name'] ?? '');
            $sheet->setCellValue('AD' . $rowNum, $r['customer_phone'] ?? '');
            $sheet->setCellValue('AE' . $rowNum, $r['note'] ?? '');
            $sheet->setCellValue('AF' . $rowNum, $total);

            // Qiymət və məbləğ formatları
            $sheet->getStyle('F' . $rowNum)->getNumberFormat()->setFormatCode('#,##0.00 "₼"');
            $sheet->getStyle('G' . $rowNum)->getNumberFormat()->setFormatCode('#,##0.00 "₼"');
            $sheet->getStyle('H' . $rowNum)->getNumberFormat()->setFormatCode('#,##0.00 "₼"');
            $sheet->getStyle('AF' . $rowNum)->getNumberFormat()->setFormatCode('#,##0.00 "₼"');

            // Alignment
            $sheet->getStyle('A' . $rowNum . ':D' . $rowNum)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            $sheet->getStyle('E' . $rowNum)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            $sheet->getStyle('K' . $rowNum . ':L' . $rowNum)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            $sheet->getStyle('N' . $rowNum . ':O' . $rowNum)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            $sheet->getStyle('Q' . $rowNum . ':R' . $rowNum)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            $sheet->getStyle('T' . $rowNum . ':U' . $rowNum)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            $sheet->getStyle('W' . $rowNum . ':X' . $rowNum)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            $sheet->getStyle('Z' . $rowNum)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            $sheet->getStyle('AB' . $rowNum)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

            // Alternating row background
            if ($rowNum % 2 == 0) {
                $sheet->getStyle('A' . $rowNum . ':AF' . $rowNum)->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setRGB('FBFBFB');
            }

            $sheet->getRowDimension($rowNum)->setRowHeight(21);
            $rowNum++;
        }

        $lastDataRow = $rowNum - 1;
        $totalRow = $rowNum;

        // 6. CƏMİ (Total Row)
        $sheet->mergeCells('A' . $totalRow . ':D' . $totalRow);
        $sheet->setCellValue('A' . $totalRow, 'CƏMİ:');
        $sheet->getStyle('A' . $totalRow)->getFont()->setBold(true)->setSize(11);
        $sheet->getStyle('A' . $totalRow)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);

        if ($lastDataRow >= 5) {
            $sheet->setCellValue('E' . $totalRow, "=SUM(E5:E{$lastDataRow})");
            $sheet->setCellValue('G' . $totalRow, "=SUM(G5:G{$lastDataRow})");
            $sheet->setCellValue('H' . $totalRow, "=SUM(H5:H{$lastDataRow})");
            $sheet->setCellValue('K' . $totalRow, "=SUM(K5:K{$lastDataRow})");
            $sheet->setCellValue('L' . $totalRow, "=SUM(L5:L{$lastDataRow})");
            $sheet->setCellValue('N' . $totalRow, "=SUM(N5:N{$lastDataRow})");
            $sheet->setCellValue('O' . $totalRow, "=SUM(O5:O{$lastDataRow})");
            $sheet->setCellValue('Q' . $totalRow, "=SUM(Q5:Q{$lastDataRow})");
            $sheet->setCellValue('R' . $totalRow, "=SUM(R5:R{$lastDataRow})");
            $sheet->setCellValue('T' . $totalRow, "=SUM(T5:T{$lastDataRow})");
            $sheet->setCellValue('U' . $totalRow, "=SUM(U5:U{$lastDataRow})");
            $sheet->setCellValue('W' . $totalRow, "=SUM(W5:W{$lastDataRow})");
            $sheet->setCellValue('X' . $totalRow, "=SUM(X5:X{$lastDataRow})");
            $sheet->setCellValue('Z' . $totalRow, "=SUM(Z5:Z{$lastDataRow})");
            $sheet->setCellValue('AB' . $totalRow, "=SUM(AB5:AB{$lastDataRow})");
            $sheet->setCellValue('AF' . $totalRow, "=SUM(AF5:AF{$lastDataRow})");
        } else {
            $sheet->setCellValue('G' . $totalRow, 0);
            $sheet->setCellValue('H' . $totalRow, 0);
            $sheet->setCellValue('AF' . $totalRow, 0);
        }

        $sheet->getStyle('G' . $totalRow)->getNumberFormat()->setFormatCode('#,##0.00 "₼"');
        $sheet->getStyle('H' . $totalRow)->getNumberFormat()->setFormatCode('#,##0.00 "₼"');
        $sheet->getStyle('AF' . $totalRow)->getNumberFormat()->setFormatCode('#,##0.00 "₼"');

        $sheet->getStyle('A' . $totalRow . ':AF' . $totalRow)->getFont()->setBold(true)->setSize(11);
        $sheet->getStyle('A' . $totalRow . ':AF' . $totalRow)->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setRGB('D4EDDA'); // Light green total
        $sheet->getRowDimension($totalRow)->setRowHeight(26);

        // 7. Borders (Bütün cədvələ səliqəli incə xətlər çəkirik)
        $styleArray = [
            'borders' => [
                'allBorders' => [
                    'borderStyle' => Border::BORDER_THIN,
                    'color' => ['rgb' => 'D0D0D0'],
                ],
            ],
        ];
        $sheet->getStyle('A3:AF' . $totalRow)->applyFromArray($styleArray);

        // Freeze Panes (Yuxarı 4 sətri dondururuq ki aşağı sürüşdürəndə başlıq sabit qalsın)
        $sheet->freezePane('A5');

        // Bütün sütunların enini avtomatik tənzimləyirik
        foreach (range('A', 'Z') as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }
        foreach (['AA', 'AB', 'AC', 'AD', 'AE', 'AF'] as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }

        $fileName = "elektron_qaime_{$branchSlug}_{$filterMonth}.xlsx";
        $headers = [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            'Content-Disposition' => "attachment; filename=\"{$fileName}\"",
            'Cache-Control' => 'max-age=0',
        ];

        return response()->stream(function () use ($spreadsheet) {
            $writer = new Xlsx($spreadsheet);
            $writer->save('php://output');
        }, 200, $headers);
    }

    /**
     * Satış massivindən cəmi məbləğləri və sayları hesablayır.
     */
    private function calculateSummary(array $sales): array
    {
        $totalSales = count($sales);
        $totalRevenue = 0;
        $totalCash = 0;
        $totalCard = 0;
        $totalPlayers = 0;
        $totalMidnight = 0;
        $totalPlusOne = 0;

        foreach ($sales as $r) {
            $cash = (float)($r['cash_amount'] ?? 0);
            $card = (float)($r['card_amount'] ?? 0);
            $totalRevenue += (float)($r['total_price'] ?? ($cash + $card));
            $totalCash += $cash;
            $totalCard += $card;
            $totalPlayers += (int)($r['player_count'] ?? 0);

            if (!empty($r['is_midnight']) || !empty($r['receptionist_bonus_00']) || !empty($r['hostess_bonus_00']) || !empty($r['actor1_bonus_00']) || !empty($r['operator_bonus_00'])) {
                $totalMidnight++;
            }
            if (!empty($r['plus_one_actor']) || !empty($r['plus_one_receptionist'])) {
                $totalPlusOne++;
            }
        }

        return [
            'total_sales' => $totalSales,
            'total_revenue' => $totalRevenue,
            'total_cash' => $totalCash,
            'total_card' => $totalCard,
            'total_players' => $totalPlayers,
            'average_price' => $totalSales > 0 ? ($totalRevenue / $totalSales) : 0,
            'total_midnight_bonuses' => $totalMidnight,
            'total_plus_one_bonuses' => $totalPlusOne,
        ];
    }

    /**
     * Bütün verilənlər bazasındakı (sales, reservations, accounting_records) mövcud ayları əldə edir.
     */
    protected function getAvailableMonths(): array
    {
        $monthNamesAz = [
            '01' => 'Yanvar',
            '02' => 'Fevral',
            '03' => 'Mart',
            '04' => 'Aprel',
            '05' => 'May',
            '06' => 'İyun',
            '07' => 'İyul',
            '08' => 'Avqust',
            '09' => 'Sentyabr',
            '10' => 'Oktyabr',
            '11' => 'Noyabr',
            '12' => 'Dekabr',
        ];

        $currentYm = Carbon::now()->format('Y-m');
        $minYm = '2025-11';
        $maxYm = $currentYm;

        // Yerli AccountingRecord-da hər hansı başqa ay varsa, onları da əhatə edirik
        try {
            $accMonths = AccountingRecord::select('month')->distinct()->pluck('month');
            foreach ($accMonths as $m) {
                if (is_string($m) && preg_match('/^\d{4}-\d{2}$/', $m)) {
                    if ($m < $minYm) $minYm = $m;
                    if ($m > $maxYm) $maxYm = $m;
                }
            }
        } catch (\Exception $e) {
            // ignore
        }

        // 2025-11-dən cari aya qədər bütün ayları ardıcıl və kəsintisiz doldururuq
        $cur = Carbon::parse($minYm . '-01');
        $end = Carbon::parse($maxYm . '-01');
        $allMonths = collect();
        while ($cur->lessThanOrEqualTo($end)) {
            $allMonths->push($cur->format('Y-m'));
            $cur->addMonth();
        }

        // Aylar üzrə ardıcıl şəkildə sıralayırıq (xronoloji: ən köhnədən cari aya doğru, cari ay ən sonda)
        $sorted = $allMonths->unique()->sort()->values();

        return $sorted->map(function ($ym) use ($monthNamesAz) {
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
