<?php

namespace App\Http\Controllers;

use App\Models\CrmInquiryStatistic;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CrmInquiryController extends Controller
{
    /**
     * Müraciət Statistikası — İllər üzrə qarmon (accordion) və aylar cədvəli.
     */
    public function index(Request $request)
    {
        $monthNamesAz = [
            1 => 'Yanvar', 2 => 'Fevral', 3 => 'Mart', 4 => 'Aprel',
            5 => 'May', 6 => 'İyun', 7 => 'İyul', 8 => 'Avqust',
            9 => 'Sentyabr', 10 => 'Oktyabr', 11 => 'Noyabr', 12 => 'Dekabr',
        ];

        $currentYear = (int) Carbon::now()->year;
        $currentMonth = (int) Carbon::now()->month;

        // Variant 3: Təqvim əsaslı avtomatik cari ayın yaradılması
        // Hər yeni ay çatanda cari ayın sətri yoxdursa (və əvvəl silinməyibsə), avtomatik yaradılır
        $hasCurrentMonth = CrmInquiryStatistic::withTrashed()
            ->where('year', $currentYear)
            ->where('month', $currentMonth)
            ->exists();

        if (!$hasCurrentMonth) {
            CrmInquiryStatistic::create([
                'year' => $currentYear,
                'month' => $currentMonth,
                'period_label' => ($monthNamesAz[$currentMonth] ?? $currentMonth) . ' ' . $currentYear,
                'cancellations' => 0,
                'discounts' => 0,
                'reservations' => 0,
                'location' => 0,
                'games' => 0,
                'working_hours' => 0,
                'complaints' => 0,
                'other' => 0,
                'total' => 0,
            ]);
        }

        $records = CrmInquiryStatistic::orderBy('year', 'desc')
            ->orderBy('month', 'asc')
            ->get();

        // İllər üzrə qruplaşdırma
        $yearsData = [];

        // Əgər heç bir il yoxdursa, ən azı cari ili yaradırıq
        $grouped = $records->groupBy('year');
        $currentYear = (int) Carbon::now()->year;

        if (!$grouped->has($currentYear)) {
            $grouped->put($currentYear, collect());
        }

        // İlləri ardıcıl azalan sıra ilə sıralayırıq
        $sortedYears = $grouped->keys()->sortDesc();

        $overallTotals = [
            'cancellations' => 0,
            'discounts' => 0,
            'reservations' => 0,
            'location' => 0,
            'games' => 0,
            'working_hours' => 0,
            'complaints' => 0,
            'other' => 0,
            'total' => 0,
        ];

        foreach ($sortedYears as $year) {
            $yearRecords = $grouped->get($year, collect());

            $yearTotals = [
                'cancellations' => 0,
                'discounts' => 0,
                'reservations' => 0,
                'location' => 0,
                'games' => 0,
                'working_hours' => 0,
                'complaints' => 0,
                'other' => 0,
                'total' => 0,
            ];

            $monthsList = [];
            foreach ($yearRecords as $r) {
                $rTotal = (int) $r->calculateTotal();
                $r->total = $rTotal;

                $monthsList[] = [
                    'id' => $r->id,
                    'year' => $r->year,
                    'month' => $r->month,
                    'period_label' => $r->period_label ?: (($monthNamesAz[$r->month] ?? $r->month) . ' ' . $r->year),
                    'cancellations' => (int) $r->cancellations,
                    'discounts' => (int) $r->discounts,
                    'reservations' => (int) $r->reservations,
                    'location' => (int) $r->location,
                    'games' => (int) $r->games,
                    'working_hours' => (int) $r->working_hours,
                    'complaints' => (int) $r->complaints,
                    'other' => (int) $r->other,
                    'total' => $rTotal,
                ];

                $yearTotals['cancellations'] += (int) $r->cancellations;
                $yearTotals['discounts'] += (int) $r->discounts;
                $yearTotals['reservations'] += (int) $r->reservations;
                $yearTotals['location'] += (int) $r->location;
                $yearTotals['games'] += (int) $r->games;
                $yearTotals['working_hours'] += (int) $r->working_hours;
                $yearTotals['complaints'] += (int) $r->complaints;
                $yearTotals['other'] += (int) $r->other;
                $yearTotals['total'] += $rTotal;

                $overallTotals['cancellations'] += (int) $r->cancellations;
                $overallTotals['discounts'] += (int) $r->discounts;
                $overallTotals['reservations'] += (int) $r->reservations;
                $overallTotals['location'] += (int) $r->location;
                $overallTotals['games'] += (int) $r->games;
                $overallTotals['working_hours'] += (int) $r->working_hours;
                $overallTotals['complaints'] += (int) $r->complaints;
                $overallTotals['other'] += (int) $r->other;
                $overallTotals['total'] += $rTotal;
            }

            // İlin tamamlanma vəziyyəti: əgər 12 ayın hamısı varsa və ya keçmiş ildir
            $isCompleted = ($year < $currentYear) || (count($monthsList) >= 12);

            $existingMonths = array_column($monthsList, 'month');
            $missingMonths = [];
            foreach ($monthNamesAz as $mNum => $mName) {
                if (!in_array($mNum, $existingMonths)) {
                    $missingMonths[$mNum] = $mName;
                }
            }

            $yearsData[$year] = [
                'year' => $year,
                'is_current' => ($year === $currentYear),
                'is_completed' => $isCompleted,
                'months_count' => count($monthsList),
                'months' => $monthsList,
                'missing_months' => $missingMonths,
                'totals' => $yearTotals,
            ];
        }

        $canEdit = auth()->user()->can('crm.edit');
        $canDelete = auth()->user()->can('crm.delete');

        return view('crm.inquiries.index', [
            'yearsData' => $yearsData,
            'overallTotals' => $overallTotals,
            'monthNamesAz' => $monthNamesAz,
            'currentYear' => $currentYear,
            'canEdit' => $canEdit,
            'canDelete' => $canDelete,
        ]);
    }

    /**
     * Xanaların kütləvi və ya cari il üzrə saxlanması (Ctrl+S və ya Yadda Saxla düyməsi).
     */
    public function save(Request $request): JsonResponse
    {
        $request->validate([
            'rows' => 'required|array',
            'rows.*.year' => 'required|integer',
            'rows.*.month' => 'required|integer|between:1,12',
        ]);

        $monthNamesAz = [
            1 => 'Yanvar', 2 => 'Fevral', 3 => 'Mart', 4 => 'Aprel',
            5 => 'May', 6 => 'İyun', 7 => 'İyul', 8 => 'Avqust',
            9 => 'Sentyabr', 10 => 'Oktyabr', 11 => 'Noyabr', 12 => 'Dekabr',
        ];

        $savedCount = 0;
        $userId = auth()->id();

        DB::beginTransaction();
        try {
            foreach ($request->input('rows') as $row) {
                $year = (int) $row['year'];
                $month = (int) $row['month'];

                $cancellations = max(0, (int) ($row['cancellations'] ?? 0));
                $discounts = max(0, (int) ($row['discounts'] ?? 0));
                $reservations = max(0, (int) ($row['reservations'] ?? 0));
                $location = max(0, (int) ($row['location'] ?? 0));
                $games = max(0, (int) ($row['games'] ?? 0));
                $workingHours = max(0, (int) ($row['working_hours'] ?? 0));
                $complaints = max(0, (int) ($row['complaints'] ?? 0));
                $other = max(0, (int) ($row['other'] ?? 0));

                $total = $cancellations + $discounts + $reservations + $location + $games + $workingHours + $complaints + $other;
                $periodLabel = $row['period_label'] ?? (($monthNamesAz[$month] ?? $month) . ' ' . $year);

                CrmInquiryStatistic::updateOrCreate(
                    [
                        'year' => $year,
                        'month' => $month,
                    ],
                    [
                        'period_label' => $periodLabel,
                        'cancellations' => $cancellations,
                        'discounts' => $discounts,
                        'reservations' => $reservations,
                        'location' => $location,
                        'games' => $games,
                        'working_hours' => $workingHours,
                        'complaints' => $complaints,
                        'other' => $other,
                        'total' => $total,
                        'updated_by' => $userId,
                    ]
                );

                $savedCount++;
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => "{$savedCount} ayın məlumatı uğurla yadda saxlanıldı!",
                'saved_count' => $savedCount,
            ]);
        } catch (\Exception $e) {
            DB::rollBack();

            return response()->json([
                'success' => false,
                'message' => 'Yadda saxlama zamanı xəta baş verdi: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Yeni ay və ya tək bir sətir əlavə et / yenilə.
     */
    public function saveRow(Request $request): JsonResponse
    {
        $request->validate([
            'year' => 'required|integer',
            'month' => 'required|integer|between:1,12',
        ]);

        $monthNamesAz = [
            1 => 'Yanvar', 2 => 'Fevral', 3 => 'Mart', 4 => 'Aprel',
            5 => 'May', 6 => 'İyun', 7 => 'İyul', 8 => 'Avqust',
            9 => 'Sentyabr', 10 => 'Oktyabr', 11 => 'Noyabr', 12 => 'Dekabr',
        ];

        $year = (int) $request->input('year');
        $month = (int) $request->input('month');

        $cancellations = max(0, (int) $request->input('cancellations', 0));
        $discounts = max(0, (int) $request->input('discounts', 0));
        $reservations = max(0, (int) $request->input('reservations', 0));
        $location = max(0, (int) $request->input('location', 0));
        $games = max(0, (int) $request->input('games', 0));
        $workingHours = max(0, (int) $request->input('working_hours', 0));
        $complaints = max(0, (int) $request->input('complaints', 0));
        $other = max(0, (int) $request->input('other', 0));

        $total = $cancellations + $discounts + $reservations + $location + $games + $workingHours + $complaints + $other;
        $periodLabel = ($monthNamesAz[$month] ?? $month) . ' ' . $year;

        $record = CrmInquiryStatistic::updateOrCreate(
            [
                'year' => $year,
                'month' => $month,
            ],
            [
                'period_label' => $periodLabel,
                'cancellations' => $cancellations,
                'discounts' => $discounts,
                'reservations' => $reservations,
                'location' => $location,
                'games' => $games,
                'working_hours' => $workingHours,
                'complaints' => $complaints,
                'other' => $other,
                'total' => $total,
                'updated_by' => auth()->id(),
            ]
        );

        return response()->json([
            'success' => true,
            'message' => "{$periodLabel} uğurla yadda saxlanıldı!",
            'record' => $record,
            'total' => $total,
        ]);
    }

    /**
     * Tələb əsasında ayı cədvələ əlavə et (və ya silinmişdirsə bərpa et).
     */
    public function addMonth(Request $request): JsonResponse
    {
        $request->validate([
            'year' => 'required|integer',
            'month' => 'required|integer|between:1,12',
        ]);

        $year = (int) $request->input('year');
        $month = (int) $request->input('month');

        $monthNamesAz = [
            1 => 'Yanvar', 2 => 'Fevral', 3 => 'Mart', 4 => 'Aprel',
            5 => 'May', 6 => 'İyun', 7 => 'İyul', 8 => 'Avqust',
            9 => 'Sentyabr', 10 => 'Oktyabr', 11 => 'Noyabr', 12 => 'Dekabr',
        ];

        $periodLabel = ($monthNamesAz[$month] ?? $month) . ' ' . $year;

        $record = CrmInquiryStatistic::withTrashed()
            ->where('year', $year)
            ->where('month', $month)
            ->first();

        if ($record) {
            if ($record->trashed()) {
                $record->restore();
            }
        } else {
            $record = CrmInquiryStatistic::create([
                'year' => $year,
                'month' => $month,
                'period_label' => $periodLabel,
                'cancellations' => 0,
                'discounts' => 0,
                'reservations' => 0,
                'location' => 0,
                'games' => 0,
                'working_hours' => 0,
                'complaints' => 0,
                'other' => 0,
                'total' => 0,
                'created_by' => auth()->id(),
            ]);
        }

        return response()->json([
            'success' => true,
            'message' => "{$periodLabel} uğurla əlavə edildi!",
            'record' => [
                'id' => $record->id,
                'year' => (int) $record->year,
                'month' => (int) $record->month,
                'period_label' => $record->period_label ?: $periodLabel,
                'cancellations' => (int) $record->cancellations,
                'discounts' => (int) $record->discounts,
                'reservations' => (int) $record->reservations,
                'location' => (int) $record->location,
                'games' => (int) $record->games,
                'working_hours' => (int) $record->working_hours,
                'complaints' => (int) $record->complaints,
                'other' => (int) $record->other,
                'total' => (int) $record->calculateTotal(),
            ],
        ]);
    }

    /**
     * Sətri sil.
     */
    public function destroy($id): JsonResponse
    {
        $record = CrmInquiryStatistic::findOrFail($id);
        $periodLabel = $record->period_label;
        $record->delete();

        return response()->json([
            'success' => true,
            'message' => "{$periodLabel} statistikası silindi.",
        ]);
    }
}
