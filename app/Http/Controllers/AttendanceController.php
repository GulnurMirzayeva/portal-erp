<?php

namespace App\Http\Controllers;

use App\Models\AttendanceBranch;
use App\Models\AttendanceLog;
use App\Models\AttendanceSetting;
use App\Models\AttendanceWorker;
use App\Services\AttendanceService;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use Symfony\Component\HttpFoundation\StreamedResponse;

class AttendanceController extends Controller
{
    public function __construct(
        protected AttendanceService $attendanceService
    ) {}

    /**
     * Gündəlik Davamiyyət Jurnalı və Filterlər.
     */
    public function index(Request $request)
    {
        // Unudulmuş açıq seansları təyin olunmuş saatda (məs: 06:00) avtomatik çıxış edirik
        $this->attendanceService->performAutoCheckout();

        $selectedDate = $request->input('date', Carbon::today()->toDateString());
        $selectedBranchId = $request->input('branch_id');
        $selectedType = $request->input('type');
        $selectedStatus = $request->input('status', 'success'); // default yalnız uğurlular və ya all

        $query = AttendanceLog::query();

        if ($selectedDate !== 'all') {
            $query->where('date', $selectedDate);
        }

        if ($selectedBranchId && $selectedBranchId !== 'all') {
            $query->where('branch_id', $selectedBranchId);
        }

        if ($selectedType && $selectedType !== 'all') {
            $query->where('type', $selectedType);
        }

        if ($selectedStatus && $selectedStatus !== 'all') {
            $query->where('status', $selectedStatus);
        }

        $logs = $query->orderBy('scanned_at', 'desc')->paginate(30)->withQueryString();

        // Statistika
        $today = Carbon::today()->toDateString();
        $stats = [
            'total_today' => AttendanceLog::where('date', $today)->where('status', 'success')->count(),
            'check_ins_today' => AttendanceLog::where('date', $today)->where('status', 'success')->where('type', 'check_in')->count(),
            'check_outs_today' => AttendanceLog::where('date', $today)->where('status', 'success')->where('type', 'check_out')->count(),
            'violations_today' => AttendanceLog::where('date', $today)->where('is_violation', true)->count(),
        ];

        $branches = AttendanceBranch::orderBy('name')->get();
        $isPinRequired = AttendanceSetting::isPinRequired();

        return view('attendance.index', compact('logs', 'stats', 'branches', 'selectedDate', 'selectedBranchId', 'selectedType', 'selectedStatus', 'isPinRequired'));
    }

    /**
     * Şübhəli və Yanlış Cəhdlər / Hiylə Jurnalı.
     */
    public function violations(Request $request)
    {
        $selectedDate = $request->input('date');
        $selectedBranchId = $request->input('branch_id');
        $selectedReason = $request->input('reason');

        $query = AttendanceLog::where(function ($q) {
            $q->where('is_violation', true)
              ->orWhere('status', '!=', 'success');
        });

        if ($selectedDate) {
            $query->where('date', $selectedDate);
        }

        if ($selectedBranchId && $selectedBranchId !== 'all') {
            $query->where('branch_id', $selectedBranchId);
        }

        if ($selectedReason && $selectedReason !== 'all') {
            $query->where('rejection_reason', $selectedReason);
        }

        $violations = $query->orderBy('scanned_at', 'desc')->paginate(30)->withQueryString();
        $branches = AttendanceBranch::orderBy('name')->get();

        $stats = [
            'total_violations' => AttendanceLog::where('is_violation', true)->count(),
            'out_of_range_count' => AttendanceLog::where('rejection_reason', 'out_of_range')->count(),
            'device_reused_count' => AttendanceLog::where('rejection_reason', 'device_already_used')->count(),
            'wrong_pin_count' => AttendanceLog::where('rejection_reason', 'wrong_pin')->count(),
        ];

        return view('attendance.violations', compact('violations', 'branches', 'stats', 'selectedDate', 'selectedBranchId', 'selectedReason'));
    }

    /**
     * Filiallar və QR Kod Tənzimləmələri.
     */
    public function branches()
    {
        $branches = AttendanceBranch::withCount([
            'activeWorkers as workers_count',
            'logs',
        ])->orderBy('name')->get();
        return view('attendance.branches', compact('branches'));
    }

    /**
     * Yeni filial və QR kod yaradır (Portal Website DB ilə sinxron saxlanılır).
     */
    public function storeBranch(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'latitude' => 'required|numeric|between:-90,90',
            'longitude' => 'required|numeric|between:-180,180',
            'map_url' => 'nullable|string',
            'work_time_weekdays_start' => 'nullable|string',
            'work_time_weekdays_end' => 'nullable|string',
            'work_time_weekend_start' => 'nullable|string',
            'work_time_weekend_end' => 'nullable|string',
            'is_active' => 'boolean',
        ]);

        $slug = Str::slug($validated['name']);
        if (AttendanceBranch::where('slug', $slug)->exists()) {
            $slug .= '-' . Str::lower(Str::random(4));
        }

        $token = AttendanceBranch::generateUniqueToken();
        $portalBranchId = null;

        $lat = (float) $validated['latitude'];
        $lng = (float) $validated['longitude'];

        // Google Maps iframe və ya linkindən təmiz embed URL çıxarılır
        $mapUrl = $request->input('map_url');
        if (!empty($mapUrl)) {
            if (preg_match('/src=["\']([^"\']+)["\']/i', $mapUrl, $mSrc)) {
                $mapUrl = $mSrc[1];
            }
        }
        if (empty($mapUrl)) {
            $mapUrl = "https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3039.0!2d{$lng}!3d{$lat}!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x0%3A0x0!2z!5e0!3m2!1saz!2saz";
        }

        // 1. Real Portal Website DB-də filial və tərcümə qeydini yaradırıq
        try {
            $portalBranchId = DB::connection('portal_website')->table('branches')->insertGetId([
                'slug' => $slug,
                'map' => $mapUrl,
                'work_time_weekdays_start' => $request->input('work_time_weekdays_start'),
                'work_time_weekdays_end' => $request->input('work_time_weekdays_end'),
                'work_time_weekend_start' => $request->input('work_time_weekend_start'),
                'work_time_weekend_end' => $request->input('work_time_weekend_end'),
                'is_open' => $request->boolean('is_active', true) ? 1 : 0,
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            foreach (['az', 'en', 'ru'] as $locale) {
                DB::connection('portal_website')->table('branch_translations')->insert([
                    'branch_id' => $portalBranchId,
                    'locale' => $locale,
                    'name' => $validated['name'],
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
        } catch (\Exception $e) {
            Log::warning("PortalWebsite DB filial yaratma xətası: " . $e->getMessage());
        }

        // 2. ERP Attendance filialını yaradırıq
        $branch = AttendanceBranch::create([
            'portal_branch_id' => $portalBranchId,
            'name' => $validated['name'],
            'slug' => $slug,
            'qr_token' => $token,
            'latitude' => $lat,
            'longitude' => $lng,
            'map_url' => $mapUrl,
            'work_time_weekdays_start' => $request->input('work_time_weekdays_start'),
            'work_time_weekdays_end' => $request->input('work_time_weekdays_end'),
            'work_time_weekend_start' => $request->input('work_time_weekend_start'),
            'work_time_weekend_end' => $request->input('work_time_weekend_end'),
            'radius_meters' => 100,
            'is_active' => $request->boolean('is_active', true),
        ]);

        return response()->json([
            'success' => true,
            'message' => "Yeni filial və QR kod uğurla yaradıldı və Portal Website bazasında saxlanıldı: {$branch->name}",
            'branch' => $branch,
        ]);
    }

    /**
     * Filial məlumatlarını yeniləyir (həm ERP, həm də Real Portal Website DB-də).
     */
    public function updateBranch(Request $request, int $id): JsonResponse
    {
        $branch = AttendanceBranch::findOrFail($id);

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'latitude' => 'required|numeric|between:-90,90',
            'longitude' => 'required|numeric|between:-180,180',
            'map_url' => 'nullable|string',
            'work_time_weekdays_start' => 'nullable|string',
            'work_time_weekdays_end' => 'nullable|string',
            'work_time_weekend_start' => 'nullable|string',
            'work_time_weekend_end' => 'nullable|string',
            'is_active' => 'boolean',
        ]);

        $lat = (float) $validated['latitude'];
        $lng = (float) $validated['longitude'];

        $mapUrl = $request->input('map_url');
        if (!empty($mapUrl)) {
            if (preg_match('/src=["\']([^"\']+)["\']/i', $mapUrl, $mSrc)) {
                $mapUrl = $mSrc[1];
            }
        }
        if (empty($mapUrl)) {
            $mapUrl = $branch->map_url ?: "https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3039.0!2d{$lng}!3d{$lat}!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x0%3A0x0!2z!5e0!3m2!1saz!2saz";
        }

        // 1. ERP Attendance cədvəlini yeniləyirik
        $branch->update([
            'name' => $validated['name'],
            'latitude' => $lat,
            'longitude' => $lng,
            'map_url' => $mapUrl,
            'work_time_weekdays_start' => $request->input('work_time_weekdays_start'),
            'work_time_weekdays_end' => $request->input('work_time_weekdays_end'),
            'work_time_weekend_start' => $request->input('work_time_weekend_start'),
            'work_time_weekend_end' => $request->input('work_time_weekend_end'),
            'is_active' => $request->boolean('is_active', true),
        ]);

        // 2. Real Portal Website DB-də map və saatları yeniləyirik
        try {
            if ($branch->portal_branch_id) {
                DB::connection('portal_website')->table('branches')
                    ->where('id', $branch->portal_branch_id)
                    ->update([
                        'map' => $mapUrl,
                        'work_time_weekdays_start' => $request->input('work_time_weekdays_start'),
                        'work_time_weekdays_end' => $request->input('work_time_weekdays_end'),
                        'work_time_weekend_start' => $request->input('work_time_weekend_start'),
                        'work_time_weekend_end' => $request->input('work_time_weekend_end'),
                        'is_open' => $request->boolean('is_active', true) ? 1 : 0,
                        'updated_at' => now(),
                    ]);

                DB::connection('portal_website')->table('branch_translations')
                    ->where('branch_id', $branch->portal_branch_id)
                    ->where('locale', 'az')
                    ->update([
                        'name' => $validated['name'],
                        'updated_at' => now(),
                    ]);
            } else {
                $portalBranchId = DB::connection('portal_website')->table('branches')->insertGetId([
                    'slug' => $branch->slug,
                    'map' => $mapUrl,
                    'work_time_weekdays_start' => $request->input('work_time_weekdays_start'),
                    'work_time_weekdays_end' => $request->input('work_time_weekdays_end'),
                    'work_time_weekend_start' => $request->input('work_time_weekend_start'),
                    'work_time_weekend_end' => $request->input('work_time_weekend_end'),
                    'is_open' => $request->boolean('is_active', true) ? 1 : 0,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);

                foreach (['az', 'en', 'ru'] as $locale) {
                    DB::connection('portal_website')->table('branch_translations')->insert([
                        'branch_id' => $portalBranchId,
                        'locale' => $locale,
                        'name' => $validated['name'],
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);
                }

                $branch->update(['portal_branch_id' => $portalBranchId]);
            }
        } catch (\Exception $e) {
            Log::warning("PortalWebsite DB filial yenilənməsi xətası: " . $e->getMessage());
        }

        return response()->json([
            'success' => true,
            'message' => "{$branch->name} filialının məlumatları və xəritəsi uğurla yeniləndi (Portal Website DB daxil olmaqla).",
            'branch' => $branch,
        ]);
    }

    /**
     * Filialı və ona aid QR kodu silir.
     */
    public function destroyBranch(int $id): JsonResponse
    {
        $branch = AttendanceBranch::findOrFail($id);
        $name = $branch->name;

        // Əlaqəli işçilərin filial bağlamasını təmizləyirik
        AttendanceWorker::where('attendance_branch_id', $branch->id)->update(['attendance_branch_id' => null]);

        $branch->delete();

        return response()->json([
            'success' => true,
            'message' => "'{$name}' filialı və QR kodu uğurla silindi.",
        ]);
    }

    /**
     * İşçilər və PIN Kodların İdarə Edilməsi.
     */
    public function workers(Request $request)
    {
        $branchId = $request->input('branch_id');
        $search = $request->input('search');
        $status = $request->input('status', 'active'); // Defolt olaraq yalnız aktiv işçilər

        $query = AttendanceWorker::with('branch');

        if ($branchId && $branchId !== 'all') {
            $query->where('attendance_branch_id', $branchId);
        }

        if ($status === 'active') {
            $query->where('is_active', true);
        } elseif ($status === 'inactive') {
            $query->where('is_active', false);
        }
        // $status === 'all' olduqda heç bir is_active filtri tətbiq olunmur

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('phone', 'like', "%{$search}%");
            });
        }

        $workers = $query->orderBy('name')->paginate(30)->withQueryString();
        $branches = AttendanceBranch::orderBy('name')->get();
        $isPinRequired = AttendanceSetting::isPinRequired();

        $stats = [
            'total' => AttendanceWorker::count(),
            'active' => AttendanceWorker::where('is_active', true)->count(),
            'inactive' => AttendanceWorker::where('is_active', false)->count(),
        ];

        return view('attendance.workers', compact('workers', 'branches', 'branchId', 'search', 'status', 'stats', 'isPinRequired'));
    }

    /**
     * İşçinin PIN kodunu yeniləyir.
     */
    public function updateWorkerPin(Request $request, int $id): JsonResponse
    {
        $worker = AttendanceWorker::findOrFail($id);

        $request->validate([
            'pin_code' => 'required|string|min:4|max:10',
        ]);

        $worker->update([
            'pin_code' => trim($request->input('pin_code')),
        ]);

        return response()->json([
            'success' => true,
            'message' => "{$worker->name} üçün yeni PIN kod təyin edildi: {$worker->pin_code}",
            'pin_code' => $worker->pin_code,
        ]);
    }

    /**
     * İşçiyə təhkim olunmuş cihazı sıfırlayır (Yeni telefon alanda).
     */
    public function resetWorkerDevice(int $id): JsonResponse
    {
        $worker = AttendanceWorker::findOrFail($id);
        $worker->update(['registered_device_uuid' => null]);

        return response()->json([
            'success' => true,
            'message' => "{$worker->name} üçün cihaz bağlaması sıfırlandı. İşçi yeni telefondan qeydiyyatdan keçə bilər.",
        ]);
    }

    /**
     * İşçinin statusunu aktiv/deaktiv edir və PortalWebsite DB ilə sinxron saxlayır.
     */
    public function toggleWorkerStatus(int $id): JsonResponse
    {
        $worker = AttendanceWorker::findOrFail($id);
        $newStatus = !$worker->is_active;
        $worker->is_active = $newStatus;
        $worker->save();

        $portalUpdated = false;
        if ($worker->portal_worker_id) {
            try {
                $portalStatus = $newStatus ? 'active' : 'archived';
                DB::connection('portal_website')->table('workers')
                    ->where('id', $worker->portal_worker_id)
                    ->update([
                        'status' => $portalStatus,
                        'updated_at' => now(),
                    ]);
                $portalUpdated = true;
            } catch (\Exception $e) {
                Log::warning("PortalWebsite DB worker status sync error: " . $e->getMessage());
            }
        }

        $statusText = $newStatus ? 'aktiv edildi' : 'deaktiv edildi';
        $syncNote = $portalUpdated ? ' (Portal Website-da da yeniləndi)' : '';

        return response()->json([
            'success' => true,
            'is_active' => $newStatus,
            'status_label' => $newStatus ? 'Aktiv' : 'Deaktiv',
            'message' => "İşçi '{$worker->name}' uğurla {$statusText}{$syncNote}.",
        ]);
    }

    /**
     * PortalWebsite-dan işçiləri və filialları yenidən çəkir.
     */
    public function syncFromPortal(): JsonResponse
    {
        $result = $this->attendanceService->syncFromPortalWebsite();
        return response()->json($result);
    }

    /**
     * Sistem Tənzimləmələri (PIN aktiv/deaktiv və s.).
     */
    public function settings()
    {
        $settings = [
            'require_pin' => AttendanceSetting::get('require_pin', '0'),
            'default_radius_meters' => AttendanceSetting::get('default_radius_meters', '100'),
            'allow_device_reuse' => AttendanceSetting::get('allow_device_reuse', '0'),
            'track_late_arrivals' => AttendanceSetting::get('track_late_arrivals', '0'),
            'work_start_time' => AttendanceSetting::get('work_start_time', '09:00'),
            'late_tolerance_minutes' => AttendanceSetting::get('late_tolerance_minutes', '15'),
            'auto_checkout_enabled' => AttendanceSetting::get('auto_checkout_enabled', '1'),
            'auto_checkout_time' => AttendanceSetting::get('auto_checkout_time', '06:00'),
        ];

        return view('attendance.settings', compact('settings'));
    }

    /**
     * Sistem tənzimləmələrini yadda saxlayır.
     */
    public function updateSettings(Request $request)
    {
        $request->validate([
            'require_pin' => 'required|in:0,1',
            'default_radius_meters' => 'required|integer|min:20|max:1000',
            'allow_device_reuse' => 'required|in:0,1',
            'track_late_arrivals' => 'required|in:0,1',
            'work_start_time' => 'required|string',
            'late_tolerance_minutes' => 'required|integer|min:0|max:120',
            'auto_checkout_enabled' => 'required|in:0,1',
            'auto_checkout_time' => 'required|string',
        ]);

        AttendanceSetting::set('require_pin', $request->input('require_pin'), 'PIN kod tələb olunsun');
        AttendanceSetting::set('default_radius_meters', $request->input('default_radius_meters'), 'Bütün filiallar üçün vahid radius');
        AttendanceSetting::set('allow_device_reuse', $request->input('allow_device_reuse'), 'Bir telefondan bir neçə işçiyə icazə');
        AttendanceSetting::set('track_late_arrivals', $request->input('track_late_arrivals'), 'Gecikmə vaxtının izlənməsi');
        AttendanceSetting::set('work_start_time', $request->input('work_start_time'), 'İşin başlama saatı');
        AttendanceSetting::set('late_tolerance_minutes', $request->input('late_tolerance_minutes'), 'Gecikmə limiti (dəqiqə)');
        AttendanceSetting::set('auto_checkout_enabled', $request->input('auto_checkout_enabled'), 'Avtomatik çıxış (Auto-checkout) aktiv/deaktiv');
        AttendanceSetting::set('auto_checkout_time', $request->input('auto_checkout_time'), 'Avtomatik çıxış saatı');

        // Tənzimləmə aktiv edildikdə dərhal unudulmuş çıxışları icra edirik
        if ($request->input('auto_checkout_enabled') === '1') {
            $this->attendanceService->performAutoCheckout();
        }

        return redirect()->route('attendance.settings')->with('success', 'Davamiyyət tənzimləmələri uğurla yeniləndi.');
    }

    /**
     * Davamiyyət jurnalını Excel (.xlsx) olaraq ixrac edir.
     */
    public function exportExcel(Request $request): StreamedResponse
    {
        $date = $request->input('date', Carbon::today()->toDateString());
        $branchId = $request->input('branch_id');

        $query = AttendanceLog::query();
        if ($date !== 'all') {
            $query->where('date', $date);
        }
        if ($branchId && $branchId !== 'all') {
            $query->where('branch_id', $branchId);
        }

        $logs = $query->orderBy('scanned_at', 'asc')->get();

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Davamiyyət');

        // Başlıqlar
        $headers = [
            '№', 'Tarix', 'Saat', 'İşçi', 'Filial', 'Növ', 'Status', 'Məsafə (m)', 'Cihaz', 'IP Ünvanı', 'Qeyd/Səbəb'
        ];
        $sheet->fromArray($headers, null, 'A1');

        // Stil
        $sheet->getStyle('A1:K1')->getFont()->setBold(true);
        $sheet->getStyle('A1:K1')->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)
            ->getStartColor()->setARGB('FF1E3A8A');
        $sheet->getStyle('A1:K1')->getFont()->getColor()->setARGB('FFFFFFFF');

        $rows = [];
        foreach ($logs as $idx => $log) {
            $isAuto = $log->isAutoCheckout();
            $timeStr = $isAuto ? '— (Avtomatik)' : ($log->scanned_at ? $log->scanned_at->format('H:i:s') : '—');
            $typeStr = $isAuto ? 'Avtomatik Çıxış' : ($log->type === 'check_in' ? 'Giriş' : ($log->type === 'check_out' ? 'Çıxış' : 'Naməlum'));
            $statusStr = $isAuto ? 'Avtomatik Bağlanıb' : ($log->status === 'success' ? 'Uğurlu' : 'İmtina');

            $rows[] = [
                $idx + 1,
                $log->date ? $log->date->format('d.m.Y') : '',
                $timeStr,
                $log->worker_name,
                $log->branch_name,
                $typeStr,
                $statusStr,
                $isAuto ? '—' : ($log->distance_meters !== null ? $log->distance_meters . ' m' : '—'),
                $isAuto ? 'Avtomatik Sistem' : ($log->device_model ?: '—'),
                $isAuto ? '—' : ($log->ip_address ?: '—'),
                $log->rejection_details ?: '',
            ];
        }

        if (!empty($rows)) {
            $sheet->fromArray($rows, null, 'A2');
        }

        foreach (range('A', 'K') as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }

        $fileName = 'Davamiyyet_' . ($date === 'all' ? 'Umumi' : $date) . '.xlsx';

        return response()->streamDownload(function () use ($spreadsheet) {
            $writer = new Xlsx($spreadsheet);
            $writer->save('php://output');
        }, $fileName, [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
        ]);
    }
}
