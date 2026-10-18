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
use Symfony\Component\HttpFoundation\HeaderUtils;
use Symfony\Component\HttpFoundation\Response;
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

        // Canlı PortalWebsite API-dən satışları çəkirik
        $result = $portalService->getAccountingReport([
            'month' => $filterMonth,
            'branch_id' => $apiBranchId,
        ]);
        $liveSales = $result['sales'] ?? [];
        $connected = $result['connected'] ?? false;
        $error = $result['error'] ?? null;

        // Yerli bazada mühasibin redaktə edib yadda saxladığı qeyd varmı yoxlayırıq (əgər refresh istənməyibsə)
        $savedRecord = null;
        if ($apiBranchId !== null && !$request->boolean('refresh')) {
            $savedRecord = AccountingRecord::where('branch_id', (string)$apiBranchId)
                ->where('month', $filterMonth)
                ->first();
        }

        if ($savedRecord && !empty($savedRecord->sales_data) && !$request->boolean('refresh')) {
            // Əgər yerli redaktələr varsa, canlı API satışları ilə birləşdiririk:
            // redaktə olunmuşlar saxlanılır, portal-a yeni əlavə edilmiş satışlar da itmir!
            $savedSales = $savedRecord->sales_data;
            $savedSalesById = [];
            foreach ($savedSales as $s) {
                if (!empty($s['id'])) {
                    $savedSalesById[(int)$s['id']] = $s;
                }
            }

            $mergedSales = [];
            $seenIds = [];
            foreach ($liveSales as $liveSale) {
                $id = !empty($liveSale['id']) ? (int)$liveSale['id'] : null;
                if ($id && isset($savedSalesById[$id])) {
                    $mergedSales[] = $savedSalesById[$id];
                    $seenIds[$id] = true;
                } else {
                    $mergedSales[] = $liveSale;
                    if ($id) $seenIds[$id] = true;
                }
            }

            foreach ($savedSales as $s) {
                $id = !empty($s['id']) ? (int)$s['id'] : null;
                if (!$id || !isset($seenIds[$id])) {
                    $mergedSales[] = $s;
                }
            }

            $sales = $portalService->enrichSalesWithIndividualDiscounts($mergedSales);
            $summary = $this->calculateSummary($sales);
            $hasCustomEdits = true;
            $lastSavedAt = $savedRecord->updated_at ? $savedRecord->updated_at->format('d.m.Y H:i') : null;
        } else {
            $sales = $portalService->enrichSalesWithIndividualDiscounts($liveSales);
            $summary = $result['summary'] ?? $this->calculateSummary($sales);
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
    public function save(Request $request, PortalWebsiteService $portalService)
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

        $updatedDbCount = 0;
        $apiErrors = [];

        // 1. Əvvəlcə əsas PortalWebsite API vasitəsilə əsas verilənlər bazasını yeniləyirik
        $apiResult = $portalService->updateSales($sales);
        if ($apiResult['success']) {
            $updatedDbCount = $apiResult['updated_count'];
        } else {
            $apiErrors[] = $apiResult['message'] ?? 'API ilə yeniləmə baş tutmadı';
        }

        // 2. Əgər API ilə yenilənməyibsə və ya lokal DB əlaqəsi aktivdirsə, birbaşa DB connection vasitəsilə də yeniləyirik
        try {
            $directDbUpdated = 0;
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
                    if (!empty($row['time']) && $row['time'] !== '—') {
                        try {
                            $updateFields['reservation_time'] = Carbon::parse($row['time'])->format('H:i:s');
                        } catch (\Exception $e) {}
                    }

                    // Say və Qiymət
                    if (isset($row['player_count'])) {
                        $updateFields['player_count'] = (int)$row['player_count'];
                        $updateFields['actual_player_count'] = (int)$row['player_count'];
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
                        $updateFields['team_review'] = (string)$row['note'];
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
                        $directDbUpdated++;
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

            if ($updatedDbCount === 0 && $directDbUpdated > 0) {
                $updatedDbCount = $directDbUpdated;
            }
        } catch (\Exception $e) {
            Log::warning("Direct database update fallback error: " . $e->getMessage());
        }

        // 3. ERP sisteminin özündə də AccountingRecord-u yeniləyirik
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
            'message' => $updatedDbCount > 0
                ? "Məlumatlar əsas Portal bazasında ({$updatedDbCount} satış) və ERP sistemində uğurla yeniləndi!"
                : "Məlumatlar ERP sistemində yadda saxlanıldı.",
            'saved_at' => $record->updated_at->format('d.m.Y H:i'),
            'summary' => $summary,
            'db_updated' => $updatedDbCount,
            'api_errors' => $apiErrors,
        ]);
    }


    /**
     * Filialın qaiməsini peşəkar Microsoft Excel (.xlsx) formatında ixrac edir (Windows və Mac üçün 100% uyğun).
     */
    public function export(Request $request, PortalWebsiteService $portalService): Response
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
        // Canlı API-dən satışları çəkirik
        $result = $portalService->getAccountingReport([
            'month' => $filterMonth,
            'branch_id' => $apiBranchId,
        ]);
        $liveSales = $result['sales'] ?? [];

        $savedRecord = null;
        if ($apiBranchId !== null) {
            $savedRecord = AccountingRecord::where('branch_id', (string)$apiBranchId)
                ->where('month', $filterMonth)
                ->first();
        }

        if ($savedRecord && !empty($savedRecord->sales_data)) {
            $savedSales = $savedRecord->sales_data;
            $savedSalesById = [];
            foreach ($savedSales as $s) {
                if (!empty($s['id'])) {
                    $savedSalesById[(int)$s['id']] = $s;
                }
            }

            $mergedSales = [];
            $seenIds = [];
            foreach ($liveSales as $liveSale) {
                $id = !empty($liveSale['id']) ? (int)$liveSale['id'] : null;
                if ($id && isset($savedSalesById[$id])) {
                    $mergedSales[] = $savedSalesById[$id];
                    $seenIds[$id] = true;
                } else {
                    $mergedSales[] = $liveSale;
                    if ($id) $seenIds[$id] = true;
                }
            }

            foreach ($savedSales as $s) {
                $id = !empty($s['id']) ? (int)$s['id'] : null;
                if (!$id || !isset($seenIds[$id])) {
                    $mergedSales[] = $s;
                }
            }
            $sales = $portalService->enrichSalesWithIndividualDiscounts($mergedSales);
        } else {
            $sales = $portalService->enrichSalesWithIndividualDiscounts($liveSales);
        }

        // CSV ixrac (Windows Excel və Mac üçün 100% uyğun)
        if ($format === 'csv') {
            $fallbackFileName = "elektron_qaime_{$branchSlug}_{$filterMonth}.csv";
            $utf8FileName = "elektron_qaime_{$branchLabel}_{$filterMonth}.csv";

            if (ob_get_level() > 0 && ob_get_length() > 0) {
                @ob_clean();
            }

            $tempCsv = tempnam(sys_get_temp_dir(), 'erp_csv_');
            $handle = fopen($tempCsv, 'w');

            // UTF-8 BOM (Windows Excel üçün mütləqdir ki, Azərbaycan şriftlərini düzgün göstərsin)
            fprintf($handle, chr(0xEF) . chr(0xBB) . chr(0xBF));

            // Windows Excel-ə sütun ayırıcısını bildirir ki bütün sətri A sütununa yığmasın
            fwrite($handle, "sep=;\r\n");

            fputcsv($handle, [
                '№', 'Tarix', 'Oyun', 'Saat', 'Say', 'Qiymət', 'Nağd', 'Terminal', 'Qrup endirimi', 'Fərdi endirimlər',
                'Resepşn', 'Say', '00:00', 'Hostes', 'Say', '00:00', 'Aktyor 1', 'Say', '00:00',
                'Aktyor 2', 'Say', '00:00', 'Operator', 'Say', '00:00', 'PlusBir Aktyor', 'Say',
                'PlusBir Resepşn', 'Say', 'Müştəri', 'Telefon', 'Qeyd', 'Ümumi Gəlir'
            ], ';', '"', "\\");

            foreach ($sales as $r) {
                $indDiscountsText = '';
                if (!empty($r['individual_discounts']) && is_array($r['individual_discounts'])) {
                    $parts = [];
                    foreach ($r['individual_discounts'] as $ind) {
                        $parts[] = ($ind['user_name'] ?? 'İstifadəçi') . ': ' . ($ind['discount_name'] ?? '');
                    }
                    $indDiscountsText = implode(', ', $parts);
                }

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
                    $indDiscountsText,
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
                ], ';', '"', "\\");
            }
            fclose($handle);

            $disposition = HeaderUtils::makeDisposition(
                HeaderUtils::DISPOSITION_ATTACHMENT,
                $utf8FileName,
                $fallbackFileName
            );

            return response()->download($tempCsv, $fallbackFileName, [
                'Content-Type' => 'text/csv; charset=UTF-8',
                'Content-Disposition' => $disposition,
                'Cache-Control' => 'max-age=0, must-revalidate',
                'Pragma' => 'public',
            ])->deleteFileAfterSend(true);
        }

        // Standart Microsoft Excel (.xlsx) generasiyası
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        // Vərəq adını təmizləyirik (Windows Excel-də qadağan olunmuş simvolları: \ / ? * : [ ] təmizləyirik)
        $cleanSheetTitle = preg_replace('/[\\\\\/*?:\[\]]/', '', $branchLabel);
        $cleanSheetTitle = mb_substr(trim($cleanSheetTitle), 0, 31);
        if (empty($cleanSheetTitle)) {
            $cleanSheetTitle = 'Qaime';
        }
        $sheet->setTitle($cleanSheetTitle);

        // Grid lines aktiv edirik
        $sheet->setShowGridLines(true);

        // 1. Sənəd Başlığı
        $sheet->mergeCells('A1:AG1');
        $sheet->setCellValue('A1', 'PORTAL GAMES — ELEKTRON SATIŞ QAİMƏSİ');
        $sheet->getStyle('A1')->getFont()->setSize(16)->setBold(true)->getColor()->setRGB('FFFFFF');
        $sheet->getStyle('A1')->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setRGB('107C41'); // Excel Green
        $sheet->getStyle('A1')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER)->setVertical(Alignment::VERTICAL_CENTER);
        $sheet->getRowDimension(1)->setRowHeight(36);

        // 2. Filial & Tarix Məlumatları
        $sheet->mergeCells('A2:AG2');
        $periodName = Carbon::parse($filterMonth . '-01')->translatedFormat('F Y');
        $sheet->setCellValue('A2', "Filial: {$branchLabel}   |   Hesabat Dövrü: {$periodName}   |   Çıxarış Tarixi: " . Carbon::now()->format('d.m.Y H:i'));
        $sheet->getStyle('A2')->getFont()->setSize(11)->setItalic(true)->getColor()->setRGB('333333');
        $sheet->getStyle('A2')->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setRGB('E8F5E9');
        $sheet->getStyle('A2')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER)->setVertical(Alignment::VERTICAL_CENTER);
        $sheet->getRowDimension(2)->setRowHeight(24);

        // 3. Kateqoriya Başlıqları (Sətir 3)
        $sheet->mergeCells('A3:J3');
        $sheet->setCellValue('A3', 'SATIŞ MƏLUMATLARI');
        $sheet->getStyle('A3')->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setRGB('107C41');
        $sheet->getStyle('A3')->getFont()->setBold(true)->getColor()->setRGB('FFFFFF');

        $sheet->mergeCells('K3:M3');
        $sheet->setCellValue('K3', 'RESEPŞN');
        $sheet->getStyle('K3')->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setRGB('4B546A');
        $sheet->getStyle('K3')->getFont()->setBold(true)->getColor()->setRGB('FFFFFF');

        $sheet->mergeCells('N3:P3');
        $sheet->setCellValue('N3', 'HOSTES');
        $sheet->getStyle('N3')->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setRGB('343A40');
        $sheet->getStyle('N3')->getFont()->setBold(true)->getColor()->setRGB('FFFFFF');

        $sheet->mergeCells('Q3:S3');
        $sheet->setCellValue('Q3', 'AKTYOR 1');
        $sheet->getStyle('Q3')->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setRGB('17A2B8');
        $sheet->getStyle('Q3')->getFont()->setBold(true)->getColor()->setRGB('FFFFFF');

        $sheet->mergeCells('T3:V3');
        $sheet->setCellValue('T3', 'AKTYOR 2');
        $sheet->getStyle('T3')->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setRGB('138496');
        $sheet->getStyle('T3')->getFont()->setBold(true)->getColor()->setRGB('FFFFFF');

        $sheet->mergeCells('W3:Y3');
        $sheet->setCellValue('W3', 'OPERATOR');
        $sheet->getStyle('W3')->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setRGB('4B546A');
        $sheet->getStyle('W3')->getFont()->setBold(true)->getColor()->setRGB('FFFFFF');

        $sheet->mergeCells('Z3:AA3');
        $sheet->setCellValue('Z3', 'PLUS-BİR AKTYOR');
        $sheet->getStyle('Z3')->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setRGB('D39E00');
        $sheet->getStyle('Z3')->getFont()->setBold(true)->getColor()->setRGB('000000');

        $sheet->mergeCells('AB3:AC3');
        $sheet->setCellValue('AB3', 'PLUS-BİR RESEPŞN');
        $sheet->getStyle('AB3')->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setRGB('D39E00');
        $sheet->getStyle('AB3')->getFont()->setBold(true)->getColor()->setRGB('000000');

        $sheet->mergeCells('AD3:AG3');
        $sheet->setCellValue('AD3', 'MÜŞTƏRİ VƏ YEKUN');
        $sheet->getStyle('AD3')->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setRGB('1E7E34');
        $sheet->getStyle('AD3')->getFont()->setBold(true)->getColor()->setRGB('FFFFFF');

        $sheet->getStyle('A3:AG3')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER)->setVertical(Alignment::VERTICAL_CENTER);
        $sheet->getRowDimension(3)->setRowHeight(22);

        // 4. Sütun Başlıqları (Sətir 4)
        $columns = [
            '№', 'Tarix', 'Oyun', 'Saat', 'Say', 'Qiymət', 'Nağd', 'Terminal', 'Qrup endirimi', 'Fərdi endirimlər',
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

        $sheet->getStyle('A4:AG4')->getFont()->setBold(true)->setSize(10);
        $sheet->getStyle('A4:AG4')->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setRGB('F1F3F5');
        $sheet->getStyle('A4:AG4')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER)->setVertical(Alignment::VERTICAL_CENTER);
        $sheet->getRowDimension(4)->setRowHeight(26);

        // 5. Məlumat Sətirləri
        $rowNum = 5;
        foreach ($sales as $r) {
            $cash = (float)($r['cash_amount'] ?? 0);
            $card = (float)($r['card_amount'] ?? 0);
            $price = (float)($r['price_per_person'] ?? 0);
            $total = (float)($r['total_price'] ?? ($cash + $card));

            $indDiscountsText = '';
            if (!empty($r['individual_discounts']) && is_array($r['individual_discounts'])) {
                $parts = [];
                foreach ($r['individual_discounts'] as $ind) {
                    $parts[] = ($ind['user_name'] ?? 'İstifadəçi') . ': ' . ($ind['discount_name'] ?? '');
                }
                $indDiscountsText = implode(', ', $parts);
            }

            $sheet->setCellValue('A' . $rowNum, $r['row_number'] ?? ($rowNum - 4));
            $sheet->setCellValue('B' . $rowNum, $r['date'] ?? '');
            $sheet->setCellValue('C' . $rowNum, $r['game_name'] ?? '');
            $sheet->setCellValue('D' . $rowNum, $r['time'] ?? '');
            $sheet->setCellValue('E' . $rowNum, (int)($r['player_count'] ?? 0));
            $sheet->setCellValue('F' . $rowNum, $price);
            $sheet->setCellValue('G' . $rowNum, $cash);
            $sheet->setCellValue('H' . $rowNum, $card);
            $sheet->setCellValue('I' . $rowNum, $r['discount_note'] ?? '');
            $sheet->setCellValue('J' . $rowNum, $indDiscountsText);

            // Resepşn
            $sheet->setCellValue('K' . $rowNum, $r['receptionist_name'] ?? '');
            $sheet->setCellValue('L' . $rowNum, (int)($r['receptionist_count'] ?? 0));
            $sheet->setCellValue('M' . $rowNum, !empty($r['receptionist_bonus_00']) ? 1 : 0);

            // Hostes
            $sheet->setCellValue('N' . $rowNum, $r['hostess_name'] ?? '');
            $sheet->setCellValue('O' . $rowNum, (int)($r['hostess_count'] ?? 0));
            $sheet->setCellValue('P' . $rowNum, !empty($r['hostess_bonus_00']) ? 1 : 0);

            // Aktyor 1
            $sheet->setCellValue('Q' . $rowNum, $r['actor1_name'] ?? '');
            $sheet->setCellValue('R' . $rowNum, (int)($r['actor1_count'] ?? 0));
            $sheet->setCellValue('S' . $rowNum, !empty($r['actor1_bonus_00']) ? 1 : 0);

            // Aktyor 2
            $sheet->setCellValue('T' . $rowNum, $r['actor2_name'] ?? '');
            $sheet->setCellValue('U' . $rowNum, (int)($r['actor2_count'] ?? 0));
            $sheet->setCellValue('V' . $rowNum, !empty($r['actor2_bonus_00']) ? 1 : 0);

            // Operator
            $sheet->setCellValue('W' . $rowNum, $r['operator_name'] ?? '');
            $sheet->setCellValue('X' . $rowNum, (int)($r['operator_count'] ?? 0));
            $sheet->setCellValue('Y' . $rowNum, !empty($r['operator_bonus_00']) ? 1 : 0);

            // PlusBir
            $sheet->setCellValue('Z' . $rowNum, $r['plus_one_actor'] ?? '');
            $sheet->setCellValue('AA' . $rowNum, (int)($r['plus_one_actor_count'] ?? 0));
            $sheet->setCellValue('AB' . $rowNum, $r['plus_one_receptionist'] ?? '');
            $sheet->setCellValue('AC' . $rowNum, (int)($r['plus_one_receptionist_count'] ?? 0));

            // Müştəri & Yekun
            $sheet->setCellValue('AD' . $rowNum, $r['customer_name'] ?? '');
            $sheet->setCellValue('AE' . $rowNum, $r['customer_phone'] ?? '');
            $sheet->setCellValue('AF' . $rowNum, $r['note'] ?? '');
            $sheet->setCellValue('AG' . $rowNum, $total);

            // Qiymət və məbləğ formatları (Windows və Mac Excel-in bütün versiyaları ilə 100% uyğun standart format)
            $sheet->getStyle('F' . $rowNum)->getNumberFormat()->setFormatCode('#,##0.00');
            $sheet->getStyle('G' . $rowNum)->getNumberFormat()->setFormatCode('#,##0.00');
            $sheet->getStyle('H' . $rowNum)->getNumberFormat()->setFormatCode('#,##0.00');
            $sheet->getStyle('AG' . $rowNum)->getNumberFormat()->setFormatCode('#,##0.00');

            // Alignment
            $sheet->getStyle('A' . $rowNum . ':D' . $rowNum)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            $sheet->getStyle('E' . $rowNum)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            $sheet->getStyle('L' . $rowNum . ':M' . $rowNum)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            $sheet->getStyle('O' . $rowNum . ':P' . $rowNum)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            $sheet->getStyle('R' . $rowNum . ':S' . $rowNum)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            $sheet->getStyle('U' . $rowNum . ':V' . $rowNum)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            $sheet->getStyle('X' . $rowNum . ':Y' . $rowNum)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            $sheet->getStyle('AA' . $rowNum)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            $sheet->getStyle('AC' . $rowNum)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

            // Alternating row background
            if ($rowNum % 2 == 0) {
                $sheet->getStyle('A' . $rowNum . ':AG' . $rowNum)->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setRGB('FBFBFB');
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
            $sheet->setCellValue('L' . $totalRow, "=SUM(L5:L{$lastDataRow})");
            $sheet->setCellValue('M' . $totalRow, "=SUM(M5:M{$lastDataRow})");
            $sheet->setCellValue('O' . $totalRow, "=SUM(O5:O{$lastDataRow})");
            $sheet->setCellValue('P' . $totalRow, "=SUM(P5:P{$lastDataRow})");
            $sheet->setCellValue('R' . $totalRow, "=SUM(R5:R{$lastDataRow})");
            $sheet->setCellValue('S' . $totalRow, "=SUM(S5:S{$lastDataRow})");
            $sheet->setCellValue('U' . $totalRow, "=SUM(U5:U{$lastDataRow})");
            $sheet->setCellValue('V' . $totalRow, "=SUM(V5:V{$lastDataRow})");
            $sheet->setCellValue('X' . $totalRow, "=SUM(X5:X{$lastDataRow})");
            $sheet->setCellValue('Y' . $totalRow, "=SUM(Y5:Y{$lastDataRow})");
            $sheet->setCellValue('AA' . $totalRow, "=SUM(AA5:AA{$lastDataRow})");
            $sheet->setCellValue('AC' . $totalRow, "=SUM(AC5:AC{$lastDataRow})");
            $sheet->setCellValue('AG' . $totalRow, "=SUM(AG5:AG{$lastDataRow})");
        } else {
            $sheet->setCellValue('G' . $totalRow, 0);
            $sheet->setCellValue('H' . $totalRow, 0);
            $sheet->setCellValue('AG' . $totalRow, 0);
        }

        $sheet->getStyle('G' . $totalRow)->getNumberFormat()->setFormatCode('#,##0.00');
        $sheet->getStyle('H' . $totalRow)->getNumberFormat()->setFormatCode('#,##0.00');
        $sheet->getStyle('AG' . $totalRow)->getNumberFormat()->setFormatCode('#,##0.00');

        $sheet->getStyle('A' . $totalRow . ':AG' . $totalRow)->getFont()->setBold(true)->setSize(11);
        $sheet->getStyle('A' . $totalRow . ':AG' . $totalRow)->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setRGB('D4EDDA'); // Light green total
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
        $sheet->getStyle('A3:AG' . $totalRow)->applyFromArray($styleArray);

        // Freeze Panes (Yuxarı 4 sətri dondururuq ki aşağı sürüşdürəndə başlıq sabit qalsın)
        $sheet->freezePane('A5');

        // Bütün sütunların enini avtomatik tənzimləyirik
        foreach (range('A', 'Z') as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }
        foreach (['AA', 'AB', 'AC', 'AD', 'AE', 'AF', 'AG'] as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }

        // Windows və Mac üçün ikili təhlükəsiz adlandırma:
        $asciiBranch = Str::slug($branchLabel, '_');
        if (empty($asciiBranch)) {
            $asciiBranch = 'Butun_Filiallar';
        }
        $fallbackFileName = "elektron_qaime_{$asciiBranch}_{$filterMonth}.xlsx";
        $utf8FileName = "elektron_qaime_{$cleanSheetTitle}_{$filterMonth}.xlsx";

        if (ob_get_level() > 0 && ob_get_length() > 0) {
            @ob_clean();
        }

        // Diskdə müvəqqəti fayla yazırıq (Zip stream-in yarımçıq qalmaması və Content-Length təminatı üçün)
        $tempPath = tempnam(sys_get_temp_dir(), 'erp_acc_');
        $writer = new Xlsx($spreadsheet);
        $writer->save($tempPath);

        $disposition = HeaderUtils::makeDisposition(
            HeaderUtils::DISPOSITION_ATTACHMENT,
            $utf8FileName,
            $fallbackFileName
        );

        return response()->download($tempPath, $fallbackFileName, [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            'Content-Disposition' => $disposition,
            'Cache-Control' => 'max-age=0, must-revalidate',
            'Pragma' => 'public',
        ])->deleteFileAfterSend(true);
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

    /**
     * PortalWebsite və ya lokal storage-da olan şəkilləri təhlükəsiz təqdim edir (həm lokal həm canlı mühit üçün).
     */
    public function portalStorage(string $path)
    {
        $cleanPath = ltrim($path, '/');
        if (str_contains($cleanPath, '..')) {
            abort(403);
        }

        // 1. Lokal qonşu PortalWebsite storage qovluğuna baxırıq
        $localWebsitePath = base_path("../../PortalWebsite/storage/app/public/{$cleanPath}");
        if (file_exists($localWebsitePath)) {
            return response()->file($localWebsitePath);
        }

        // 2. Lokal PortalErp storage qovluğuna baxırıq
        $localErpPath = storage_path("app/public/{$cleanPath}");
        if (file_exists($localErpPath)) {
            return response()->file($localErpPath);
        }

        // 3. Canlı portal.land serverinə yönləndiririk
        $portalBaseUrl = rtrim(config('services.portal_website.url', 'https://portal.land'), '/');
        return redirect()->away("{$portalBaseUrl}/storage/{$cleanPath}");
    }
}

