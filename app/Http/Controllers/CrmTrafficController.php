<?php

namespace App\Http\Controllers;

use App\Models\CrmTrafficStatistic;
use App\Services\PortalWebsiteService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class CrmTrafficController extends Controller
{
    /**
     * Trafik və Sosial Media Analitikası Əsas Səhifəsi.
     */
    public function index(Request $request, PortalWebsiteService $portalService)
    {
        $availableMonths = $this->getAvailableMonths();

        // Cari və ya seçilmiş ay (Defolt cari ay)
        $filterMonth = $request->query('month');
        if (empty($filterMonth) || !preg_match('/^\d{4}-\d{2}$/', $filterMonth)) {
            $filterMonth = Carbon::now()->format('Y-m');
        }

        [$year, $month] = explode('-', $filterMonth);
        $year = (int)$year;
        $month = (int)$month;

        $dt = Carbon::createFromDate($year, $month, 1);
        $daysInMonth = $dt->daysInMonth;
        $periodDisplay = $this->getMonthDisplayName($filterMonth, $availableMonths);

        // 1. Mövcud bazadakı qeydləri çəkirik
        $existingRecords = CrmTrafficStatistic::where('year', $year)
            ->where('month', $month)
            ->orderBy('day', 'asc')
            ->get()
            ->keyBy('day');

        // 2. Veb-sayt göstəriciləri üçün Portal Website API yoxlanışı
        $apiTraffic = $portalService->getWebsiteTraffic($filterMonth);
        $apiConnected = $apiTraffic['connected'] ?? false;
        $apiDailyData = $apiTraffic['daily_data'] ?? [];

        // 3. Günbəgün 1-dən $daysInMonth-a qədər massiv formalaşdırırıq
        $dailyRows = [];
        for ($d = 1; $d <= $daysInMonth; $d++) {
            $dateStr = sprintf('%04d-%02d-%02d', $year, $month, $d);
            $rec = $existingRecords->get($d);

            // API-dən gələn sayt istifadəçi və daxilolma məlumatları (əgər varsa)
            $apiUsers = $apiDailyData[$d]['users'] ?? ($apiDailyData[$dateStr]['users'] ?? null);
            $apiVisits = $apiDailyData[$d]['visits'] ?? ($apiDailyData[$dateStr]['visits'] ?? null);

            $instMsg = $rec ? (int)$rec->instagram_messages : 0;
            $instNew = $rec ? (int)$rec->instagram_followers_new : 0;
            $instLost = $rec ? (int)$rec->instagram_followers_lost : 0;
            $instNet = $rec ? (int)$rec->instagram_followers_net : ($instNew - $instLost);
            $calls = $rec ? (int)$rec->calls : 0;
            $tiktok = $rec ? (int)$rec->tiktok_followers_net : 0;
            $webUsers = $apiUsers !== null ? (int)$apiUsers : ($rec ? (int)$rec->website_users : 0);
            $webVisits = $apiVisits !== null ? (int)$apiVisits : ($rec ? (int)$rec->website_visits : 0);

            $dailyRows[$d] = [
                'day' => $d,
                'date' => $dateStr,
                'date_formatted' => sprintf('%02d.%02d.%04d', $d, $month, $year),
                'day_name' => Carbon::parse($dateStr)->locale('az')->isoFormat('dd'),
                'is_weekend' => Carbon::parse($dateStr)->isWeekend(),
                'instagram_messages' => $instMsg,
                'instagram_followers_new' => $instNew,
                'instagram_followers_lost' => $instLost,
                'instagram_followers_net' => $instNet,
                'calls' => $calls,
                'tiktok_followers_net' => $tiktok,
                'website_users' => $webUsers,
                'website_visits' => $webVisits,
                'notes' => $rec->notes ?? '',
            ];
        }

        // 4. Aylıq Yekun Cəmlər
        $totals = [
            'instagram_messages' => array_sum(array_column($dailyRows, 'instagram_messages')),
            'instagram_followers_new' => array_sum(array_column($dailyRows, 'instagram_followers_new')),
            'instagram_followers_lost' => array_sum(array_column($dailyRows, 'instagram_followers_lost')),
            'instagram_followers_net' => array_sum(array_column($dailyRows, 'instagram_followers_net')),
            'calls' => array_sum(array_column($dailyRows, 'calls')),
            'tiktok_followers_net' => array_sum(array_column($dailyRows, 'tiktok_followers_net')),
            'website_users' => array_sum(array_column($dailyRows, 'website_users')),
            'website_visits' => array_sum(array_column($dailyRows, 'website_visits')),
        ];

        // 5. Qrafiklər üçün kateqoriyalar (1..daysInMonth) və seriyalar
        $chartDays = array_map('strval', range(1, $daysInMonth));

        $chartData = [
            'days' => $chartDays,
            'instagram_messages' => array_column($dailyRows, 'instagram_messages'),
            'instagram_followers_net' => array_column($dailyRows, 'instagram_followers_net'),
            'calls' => array_column($dailyRows, 'calls'),
            'tiktok_followers_net' => array_column($dailyRows, 'tiktok_followers_net'),
            'website_users' => array_column($dailyRows, 'website_users'),
            'website_visits' => array_column($dailyRows, 'website_visits'),
        ];

        $canEdit = auth()->user()->can('crm.edit');

        return view('crm.traffic.index', [
            'filterMonth' => $filterMonth,
            'availableMonths' => $availableMonths,
            'periodDisplay' => $periodDisplay,
            'dailyRows' => $dailyRows,
            'totals' => $totals,
            'chartData' => $chartData,
            'apiConnected' => $apiConnected,
            'canEdit' => $canEdit,
        ]);
    }

    /**
     * Bütün aylıq sətirləri toplu şəkildə yadda saxlayır.
     */
    public function save(Request $request)
    {
        $validated = $request->validate([
            'month' => 'required|string|regex:/^\d{4}-\d{2}$/',
            'rows' => 'required|array',
            'rows.*.day' => 'required|integer|min:1|max:31',
            'rows.*.instagram_messages' => 'nullable|integer',
            'rows.*.instagram_followers_new' => 'nullable|integer',
            'rows.*.instagram_followers_lost' => 'nullable|integer',
            'rows.*.calls' => 'nullable|integer',
            'rows.*.tiktok_followers_net' => 'nullable|integer',
            'rows.*.website_users' => 'nullable|integer',
            'rows.*.website_visits' => 'nullable|integer',
            'rows.*.notes' => 'nullable|string|max:500',
        ]);

        [$year, $month] = explode('-', $validated['month']);
        $year = (int)$year;
        $month = (int)$month;
        $userId = auth()->id();

        DB::beginTransaction();
        try {
            foreach ($validated['rows'] as $r) {
                $day = (int)$r['day'];
                $dateStr = sprintf('%04d-%02d-%02d', $year, $month, $day);

                $instNew = (int)($r['instagram_followers_new'] ?? 0);
                $instLost = (int)($r['instagram_followers_lost'] ?? 0);
                $instNet = $instNew - $instLost;

                CrmTrafficStatistic::updateOrCreate(
                    ['date' => $dateStr],
                    [
                        'year' => $year,
                        'month' => $month,
                        'day' => $day,
                        'instagram_messages' => (int)($r['instagram_messages'] ?? 0),
                        'instagram_followers_new' => $instNew,
                        'instagram_followers_lost' => $instLost,
                        'instagram_followers_net' => $instNet,
                        'calls' => (int)($r['calls'] ?? 0),
                        'tiktok_followers_net' => (int)($r['tiktok_followers_net'] ?? 0),
                        'website_users' => (int)($r['website_users'] ?? 0),
                        'website_visits' => (int)($r['website_visits'] ?? 0),
                        'notes' => $r['notes'] ?? null,
                        'updated_by' => $userId,
                    ]
                );
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Gündəlik trafik və sosial media məlumatları uğurla yadda saxlanıldı.',
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error("Traffic save error: " . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Xəta baş verdi: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Tək bir günün göstəricisini sürətli yadda saxlayır (Inline autosave).
     */
    public function saveRow(Request $request)
    {
        $validated = $request->validate([
            'date' => 'required|date_format:Y-m-d',
            'field' => 'required|string',
            'value' => 'nullable',
        ]);

        $dateStr = $validated['date'];
        $field = $validated['field'];
        $value = $validated['value'];

        $allowedFields = [
            'instagram_messages',
            'instagram_followers_new',
            'instagram_followers_lost',
            'calls',
            'tiktok_followers_net',
            'website_users',
            'website_visits',
            'notes',
        ];

        if (!in_array($field, $allowedFields)) {
            return response()->json(['success' => false, 'message' => 'Yolverilməz sahə.'], 422);
        }

        $dt = Carbon::parse($dateStr);
        $record = CrmTrafficStatistic::firstOrNew(['date' => $dateStr]);
        $record->year = $dt->year;
        $record->month = $dt->month;
        $record->day = $dt->day;

        if ($field === 'notes') {
            $record->notes = $value;
        } else {
            $record->{$field} = (int)$value;
        }

        if (in_array($field, ['instagram_followers_new', 'instagram_followers_lost'])) {
            $record->recalculateInstagramNet();
        }

        $record->updated_by = auth()->id();
        $record->save();

        return response()->json([
            'success' => true,
            'message' => 'Dəyişiklik saxlanıldı.',
            'record' => $record,
        ]);
    }

    /**
     * Mövcud ayların siyahısı (Sayt var olandan: Noyabr 2025-dən cari aya qədər).
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

    protected function getMonthDisplayName(string $filterMonth, array $availableMonths): string
    {
        foreach ($availableMonths as $m) {
            if ($m['key'] === $filterMonth) {
                return $m['name'];
            }
        }
        return $filterMonth;
    }
}
