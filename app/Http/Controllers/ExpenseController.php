<?php

namespace App\Http\Controllers;

use App\Models\ExpenseClassification;
use App\Models\ExpenseRecord;
use App\Services\PortalWebsiteService;
use Carbon\Carbon;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ExpenseController extends Controller
{
    /**
     * Xərclərin Excel cədvəli interfeysi
     */
    public function index(Request $request, PortalWebsiteService $portalService)
    {
        $availableMonths = $this->getAvailableMonths();

        // Cari ay default
        $filterMonth = $request->query('month');
        if (empty($filterMonth) || !preg_match('/^\d{4}-\d{2}$/', $filterMonth)) {
            $filterMonth = Carbon::now()->format('Y-m');
        }

        $filterBranch = $request->query('branch_id');

        // Bütün filialları əldə edirik ("Ofis" filialını çıxmaqla)
        $allBranches = $portalService->getBranches();
        $branches = array_values(array_filter($allBranches, function ($b) {
            $name = mb_strtolower($b['name'] ?? '');
            return !str_contains($name, 'ofis') && !str_contains($name, 'office');
        }));

        if (($filterBranch === null || $filterBranch == 3) && !empty($branches)) {
            $filterBranch = $branches[0]['id'];
        }

        $selectedBranchName = 'Bütün Filiallar';
        if ($filterBranch && $filterBranch !== 'all') {
            foreach ($branches as $b) {
                if ($b['id'] == $filterBranch) {
                    $selectedBranchName = $b['name'];
                    break;
                }
            }
        }

        // Ayın adı
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

        // ERP-dən aktiv təsnifatları çəkirik
        $classifications = ExpenseClassification::where('is_active', true)
            ->orderBy('sort_order', 'asc')
            ->orderBy('id', 'asc')
            ->pluck('name')
            ->toArray();

        if (empty($classifications)) {
            $classifications = [
                'Maaş', 'Bonus', 'Premiya', 'Avans', 'Vergi', 'Arenda',
                'Aylıq xərc', 'Тех', 'Taksi', 'PPX 1', 'PPX 2', 'Uçastkovı',
                'Reklam', 'Franşiza', 'Mal-material', 'FHN', 'Depozit 1 %',
                'Kredit', 'Komissiya kartı', 'Питание', 'Yeni oyun', 'Sığorta yığım', 'Digər'
            ];
        }

        // 'Digər' ən sonda olmalıdır
        if (in_array('Digər', $classifications)) {
            $classifications = array_values(array_diff($classifications, ['Digər']));
            $classifications[] = 'Digər';
        }

        // Filialın oyunları
        $branchGames = [];
        if ($filterBranch && $filterBranch !== 'all') {
            $branchGames = $portalService->getBranchGames((int)$filterBranch);
        }

        // Əvvəlcə yerli bazada yadda saxlanılmış redaktə varmı yoxlayırıq
        $savedRecord = null;
        if ($filterBranch && $filterBranch !== 'all') {
            $savedRecord = ExpenseRecord::where('branch_id', (string)$filterBranch)
                ->where('month', $filterMonth)
                ->first();
        }

        if ($savedRecord && !empty($savedRecord->expenses_data)) {
            $expenses = $savedRecord->expenses_data;
            $summary = $savedRecord->summary_data ?? $this->calculateSummary($expenses);
            $connected = true;
            $hasCustomEdits = true;
            $lastSavedAt = $savedRecord->updated_at ? $savedRecord->updated_at->format('d.m.Y H:i') : null;
        } else {
            $report = $portalService->getExpensesReport([
                'branch_id' => $filterBranch,
                'month' => $filterMonth,
            ]);

            $expenses = $report['expenses'] ?? [];
            $summary = $report['summary'] ?? $this->calculateSummary($expenses);
            $connected = $report['connected'] ?? true;
            $hasCustomEdits = false;
            $lastSavedAt = null;

            if (empty($branchGames) && !empty($report['games'])) {
                $branchGames = $report['games'];
            }
        }

        return view('expenses.index', [
            'expenses' => $expenses,
            'summary' => $summary,
            'branches' => $branches,
            'availableMonths' => $availableMonths,
            'selectedMonthName' => $selectedMonthName,
            'filterMonth' => $filterMonth,
            'filterBranch' => $filterBranch,
            'selectedBranchName' => $selectedBranchName,
            'classifications' => $classifications,
            'branchGames' => $branchGames,
            'connected' => $connected,
            'hasCustomEdits' => $hasCustomEdits,
            'lastSavedAt' => $lastSavedAt,
        ]);
    }

    /**
     * Excel xanalarında dəyişiklikləri yadda saxla
     */
    public function save(Request $request, PortalWebsiteService $portalService)
    {
        $request->validate([
            'branch_id' => 'required',
            'month' => 'required|string',
            'expenses' => 'required|array',
        ]);

        $branchId = $request->input('branch_id');
        $month = $request->input('month');
        $expenses = $request->input('expenses');
        $summary = $this->calculateSummary($expenses);

        // 1. PortalWebsite API / DB vasitəsilə yeniləyirik
        $result = $portalService->updateExpenses($expenses, $branchId, $month);

        // 2. ERP-nin özündə də snapshot saxlayırıq
        ExpenseRecord::updateOrCreate(
            [
                'branch_id' => (string)$branchId,
                'month' => $month,
            ],
            [
                'expenses_data' => $expenses,
                'summary_data' => $summary,
                'updated_by' => auth()->id(),
            ]
        );

        return response()->json([
            'success' => true,
            'message' => $result['message'] ?? 'Xərclər uğurla yadda saxlanıldı.',
            'summary' => $summary,
            'saved_at' => Carbon::now()->format('d.m.Y H:i'),
        ]);
    }

    /**
     * Excel (.xlsx) faylı kimi ixrac et
     */
    public function export(Request $request, PortalWebsiteService $portalService): StreamedResponse
    {
        $filterMonth = $request->query('month', Carbon::now()->format('Y-m'));
        $filterBranch = $request->query('branch_id');

        $allBranches = $portalService->getBranches();
        $selectedBranchName = 'Filial';
        foreach ($allBranches as $b) {
            if ($b['id'] == $filterBranch) {
                $selectedBranchName = $b['name'];
                break;
            }
        }

        // Məlumatları çəkirik
        $savedRecord = ExpenseRecord::where('branch_id', (string)$filterBranch)
            ->where('month', $filterMonth)
            ->first();

        if ($savedRecord && !empty($savedRecord->expenses_data)) {
            $expenses = $savedRecord->expenses_data;
        } else {
            $report = $portalService->getExpensesReport([
                'branch_id' => $filterBranch,
                'month' => $filterMonth,
            ]);
            $expenses = $report['expenses'] ?? [];
        }

        $summary = $this->calculateSummary($expenses);
        $grandTotal = $summary['grand_total'] ?? 0;

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle(mb_substr($selectedBranchName, 0, 31));

        // 1-ci sətir: Yaşıl Banner (A1:I1)
        // Sol/Mərkəz: Xərclər Sahil | Sağ: ₼17 535,33
        $sheet->mergeCells('A1:G1');
        $sheet->setCellValue('A1', "Xərclər {$selectedBranchName}");
        $sheet->mergeCells('H1:I1');
        $sheet->setCellValue('H1', '₼' . number_format($grandTotal, 2, ',', ' '));

        $sheet->getStyle('A1:I1')->applyFromArray([
            'fill' => [
                'fillType' => Fill::FILL_SOLID,
                'startColor' => ['rgb' => 'D9F286'], // Açıq sarı-yaşıl banner
            ],
            'font' => [
                'bold' => true,
                'size' => 14,
                'color' => ['rgb' => '000000'],
            ],
            'alignment' => [
                'vertical' => Alignment::VERTICAL_CENTER,
            ],
        ]);
        $sheet->getStyle('A1')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $sheet->getStyle('H1')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);
        $sheet->getRowDimension(1)->setRowHeight(32);

        // 2-ci sətir: Çəhrayı Başlıqlar (A2:I2)
        $headers = [
            'A2' => '№',
            'B2' => 'Tarix',
            'C2' => 'Oyun',
            'D2' => 'Xərc',
            'E2' => 'Qeyd',
            'F2' => 'Məbləğ nəğd',
            'G2' => 'Məbləğ nəğdsiz',
            'H2' => 'Təsnifat',
            'I2' => 'Cəmi Məbləğ',
        ];

        foreach ($headers as $cell => $text) {
            $sheet->setCellValue($cell, $text);
        }

        $sheet->getStyle('A2:I2')->applyFromArray([
            'fill' => [
                'fillType' => Fill::FILL_SOLID,
                'startColor' => ['rgb' => 'F7B4CE'], // Çəhrayı başlıq rəngi
            ],
            'font' => [
                'bold' => true,
                'size' => 11,
                'color' => ['rgb' => '000000'],
            ],
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_CENTER,
                'vertical' => Alignment::VERTICAL_CENTER,
            ],
            'borders' => [
                'allBorders' => [
                    'borderStyle' => Border::BORDER_THIN,
                    'color' => ['rgb' => 'B0B0B0'],
                ],
            ],
        ]);
        $sheet->getRowDimension(2)->setRowHeight(26);

        // Sətirləri doldururuq
        $rowNum = 3;
        foreach ($expenses as $idx => $row) {
            $cash = (float)($row['amount_cash'] ?? 0);
            $card = (float)($row['amount_card'] ?? 0);
            $total = $cash + $card;

            $sheet->setCellValue("A{$rowNum}", $idx + 1);
            $sheet->setCellValue("B{$rowNum}", $row['date'] ?? '');
            $sheet->setCellValue("C{$rowNum}", $row['game_name'] ?? ($row['game_id'] ? 'Oyun #' . $row['game_id'] : 'Ümumi'));
            $sheet->setCellValue("D{$rowNum}", $row['title'] ?? '');
            $sheet->setCellValue("E{$rowNum}", $row['note'] ?? '');
            $sheet->setCellValue("F{$rowNum}", $cash > 0 ? $cash : '');
            $sheet->setCellValue("G{$rowNum}", $card > 0 ? $card : '');
            $sheet->setCellValue("H{$rowNum}", $row['classification'] ?? '');
            $sheet->setCellValue("I{$rowNum}", "=SUM(F{$rowNum}:G{$rowNum})");

            // Formatlaşdırma
            $sheet->getStyle("A{$rowNum}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            $sheet->getStyle("B{$rowNum}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            $sheet->getStyle("C{$rowNum}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_LEFT);
            $sheet->getStyle("D{$rowNum}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_LEFT);
            $sheet->getStyle("E{$rowNum}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_LEFT);
            $sheet->getStyle("F{$rowNum}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);
            $sheet->getStyle("G{$rowNum}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);
            $sheet->getStyle("H{$rowNum}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_LEFT);
            $sheet->getStyle("I{$rowNum}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);

            $sheet->getStyle("F{$rowNum}:G{$rowNum}")->getNumberFormat()->setFormatCode('#,##0.00');
            $sheet->getStyle("I{$rowNum}")->getNumberFormat()->setFormatCode('#,##0.00');

            $sheet->getStyle("A{$rowNum}:I{$rowNum}")->applyFromArray([
                'borders' => [
                    'allBorders' => [
                        'borderStyle' => Border::BORDER_THIN,
                        'color' => ['rgb' => 'D4D4D4'],
                    ],
                ],
            ]);

            $rowNum++;
        }

        // Yekun Cəmi Sətri
        $totalRow = $rowNum;
        $sheet->mergeCells("A{$totalRow}:E{$totalRow}");
        $sheet->setCellValue("A{$totalRow}", 'YEKUN CƏMİ:');
        $sheet->setCellValue("F{$totalRow}", "=SUM(F3:F" . ($totalRow - 1) . ")");
        $sheet->setCellValue("G{$totalRow}", "=SUM(G3:G" . ($totalRow - 1) . ")");
        $sheet->setCellValue("H{$totalRow}", '');
        $sheet->setCellValue("I{$totalRow}", "=SUM(I3:I" . ($totalRow - 1) . ")");

        $sheet->getStyle("A{$totalRow}:I{$totalRow}")->applyFromArray([
            'font' => ['bold' => true],
            'fill' => [
                'fillType' => Fill::FILL_SOLID,
                'startColor' => ['rgb' => 'E8F5E9'],
            ],
            'borders' => [
                'top' => ['borderStyle' => Border::BORDER_MEDIUM, 'color' => ['rgb' => '107C41']],
                'bottom' => ['borderStyle' => Border::BORDER_DOUBLE, 'color' => ['rgb' => '107C41']],
            ],
        ]);
        $sheet->getStyle("A{$totalRow}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);
        $sheet->getStyle("F{$totalRow}:G{$totalRow}")->getNumberFormat()->setFormatCode('#,##0.00');
        $sheet->getStyle("I{$totalRow}")->getNumberFormat()->setFormatCode('#,##0.00');

        // Sütun genişlikləri
        $columnWidths = [
            'A' => 6,
            'B' => 14,
            'C' => 18,
            'D' => 24,
            'E' => 24,
            'F' => 16,
            'G' => 16,
            'H' => 20,
            'I' => 16,
        ];
        foreach ($columnWidths as $col => $w) {
            $sheet->getColumnDimension($col)->setWidth($w);
        }

        $fileName = "Xercler_{$selectedBranchName}_{$filterMonth}.xlsx";

        return new StreamedResponse(function () use ($spreadsheet) {
            $writer = new Xlsx($spreadsheet);
            $writer->save('php://output');
        }, 200, [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            'Content-Disposition' => "attachment; filename=\"{$fileName}\"",
            'Cache-Control' => 'max-age=0',
        ]);
    }

    /**
     * Xərclərin cəmini hesabla
     */
    private function calculateSummary(array $expenses): array
    {
        $totalCash = 0;
        $totalCard = 0;

        foreach ($expenses as $row) {
            $totalCash += (float)($row['amount_cash'] ?? 0);
            $totalCard += (float)($row['amount_card'] ?? 0);
        }

        return [
            'total_count' => count($expenses),
            'total_cash' => $totalCash,
            'total_card' => $totalCard,
            'grand_total' => $totalCash + $totalCard,
        ];
    }

    /**
     * Əlçatan ayların siyahısı
     */
    private function getAvailableMonths(): array
    {
        $months = [];
        $monthNamesAz = [
            '01' => 'Yanvar', '02' => 'Fevral', '03' => 'Mart', '04' => 'Aprel',
            '05' => 'May', '06' => 'İyun', '07' => 'İyul', '08' => 'Avqust',
            '09' => 'Sentyabr', '10' => 'Oktyabr', '11' => 'Noyabr', '12' => 'Dekabr',
        ];

        $now = Carbon::now();
        for ($i = -6; $i <= 6; $i++) {
            $dt = $now->copy()->addMonths($i);
            $key = $dt->format('Y-m');
            $months[] = [
                'key' => $key,
                'name' => ($monthNamesAz[$dt->format('m')] ?? $dt->format('m')) . ' ' . $dt->format('Y'),
                'is_current' => $dt->isSameMonth($now),
            ];
        }

        return $months;
    }
}
