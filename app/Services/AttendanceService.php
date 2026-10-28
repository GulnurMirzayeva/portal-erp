<?php

namespace App\Services;

use App\Models\AttendanceBranch;
use App\Models\AttendanceLog;
use App\Models\AttendanceSetting;
use App\Models\AttendanceWorker;
use Carbon\Carbon;
use Exception;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class AttendanceService
{
    /**
     * İki GPS nöqtəsi arasındakı məsafəni Haversine düsturu ilə (metrlə) hesablayır.
     */
    public function calculateDistance(float $lat1, float $lon1, float $lat2, float $lon2): float
    {
        $earthRadius = 6371000; // metr

        $latFrom = deg2rad($lat1);
        $lonFrom = deg2rad($lon1);
        $latTo = deg2rad($lat2);
        $lonTo = deg2rad($lon2);

        $latDelta = $latTo - $latFrom;
        $lonDelta = $lonTo - $lonFrom;

        $angle = 2 * asin(sqrt(pow(sin($latDelta / 2), 2) +
            cos($latFrom) * cos($latTo) * pow(sin($lonDelta / 2), 2)));

        return round($angle * $earthRadius, 2);
    }

    /**
     * İşçinin QR skan və giriş-çıxış sorğusunu emal edir və bütün təhlükəsizlik qaydalarını yoxlayır.
     */
    public function processAttendance(
        string $branchToken,
        int $workerId,
        ?string $pinCode,
        ?float $userLat,
        ?float $userLng,
        ?string $deviceUuid,
        ?string $deviceModel,
        ?string $ipAddress,
        ?string $userAgent,
        ?string $forceType = null
    ): array {
        // Unudulmuş girişlər üçün vaxtı çatmış avtomatik çıxışları icra edirik
        $this->performAutoCheckout();

        $now = Carbon::now();
        $today = $now->toDateString();

        // 1. Filialı yoxlayırıq
        $branch = AttendanceBranch::where('qr_token', $branchToken)->first();
        if (!$branch || !$branch->is_active) {
            return [
                'success' => false,
                'message' => 'Filial aktiv deyil və ya QR kod etibarsızdır.',
                'reason' => 'invalid_branch',
            ];
        }

        // 2. İşçini yoxlayırıq
        $worker = AttendanceWorker::find($workerId);
        if (!$worker || !$worker->is_active) {
            $this->logAttempt([
                'worker_id' => $workerId,
                'worker_name' => $worker ? $worker->name : 'Naməlum İşçi',
                'branch_id' => $branch->id,
                'branch_name' => $branch->name,
                'date' => $today,
                'type' => 'unknown',
                'scanned_at' => $now,
                'latitude' => $userLat,
                'longitude' => $userLng,
                'distance_meters' => null,
                'device_uuid' => $deviceUuid,
                'device_model' => $deviceModel,
                'ip_address' => $ipAddress,
                'user_agent' => $userAgent,
                'status' => 'rejected',
                'rejection_reason' => 'inactive_worker',
                'rejection_details' => 'Deaktiv və ya mövcud olmayan işçi cəhdi.',
                'is_violation' => true,
            ]);

            return [
                'success' => false,
                'message' => 'İşçi tapılmadı və ya statusu deaktivdir.',
                'reason' => 'inactive_worker',
            ];
        }

        // Hərəkət növünü təyin edirik (Giriş və ya Çıxış)
        $actionType = $forceType ?: $worker->next_action;

        // 3. PIN Kod yoxlanışı (Əgər sistemdə aktivdirsə)
        $isPinRequired = AttendanceSetting::isPinRequired();
        if ($isPinRequired) {
            $cleanEnteredPin = trim((string) $pinCode);
            $cleanWorkerPin = trim((string) $worker->pin_code);

            if (empty($cleanEnteredPin)) {
                $this->logAttempt([
                    'worker_id' => $worker->id,
                    'worker_name' => $worker->name,
                    'branch_id' => $branch->id,
                    'branch_name' => $branch->name,
                    'date' => $today,
                    'type' => $actionType,
                    'scanned_at' => $now,
                    'latitude' => $userLat,
                    'longitude' => $userLng,
                    'distance_meters' => null,
                    'device_uuid' => $deviceUuid,
                    'device_model' => $deviceModel,
                    'ip_address' => $ipAddress,
                    'user_agent' => $userAgent,
                    'status' => 'rejected',
                    'rejection_reason' => 'pin_required',
                    'rejection_details' => 'PIN kod daxil edilməyib.',
                    'is_violation' => false,
                ]);

                return [
                    'success' => false,
                    'message' => 'Zəhmət olmasa 4 rəqəmli PIN kodunuzu daxil edin.',
                    'reason' => 'pin_required',
                ];
            }

            if (!empty($cleanWorkerPin) && $cleanEnteredPin !== $cleanWorkerPin) {
                $this->logAttempt([
                    'worker_id' => $worker->id,
                    'worker_name' => $worker->name,
                    'branch_id' => $branch->id,
                    'branch_name' => $branch->name,
                    'date' => $today,
                    'type' => $actionType,
                    'scanned_at' => $now,
                    'latitude' => $userLat,
                    'longitude' => $userLng,
                    'distance_meters' => null,
                    'device_uuid' => $deviceUuid,
                    'device_model' => $deviceModel,
                    'ip_address' => $ipAddress,
                    'user_agent' => $userAgent,
                    'status' => 'rejected',
                    'rejection_reason' => 'wrong_pin',
                    'rejection_details' => "Daxil edilən PIN kod yanlışdır: {$cleanEnteredPin}",
                    'is_violation' => true,
                ]);

                return [
                    'success' => false,
                    'message' => 'Daxil etdiyiniz PIN kod yanlışdır!',
                    'reason' => 'wrong_pin',
                ];
            }
        }

        // 4. GPS və Məsafə Yoxlanışı (Geofencing)
        if ($userLat === null || $userLng === null) {
            $this->logAttempt([
                'worker_id' => $worker->id,
                'worker_name' => $worker->name,
                'branch_id' => $branch->id,
                'branch_name' => $branch->name,
                'date' => $today,
                'type' => $actionType,
                'scanned_at' => $now,
                'latitude' => null,
                'longitude' => null,
                'distance_meters' => null,
                'device_uuid' => $deviceUuid,
                'device_model' => $deviceModel,
                'ip_address' => $ipAddress,
                'user_agent' => $userAgent,
                'status' => 'rejected',
                'rejection_reason' => 'location_permission_denied',
                'rejection_details' => 'Brauzer GPS məkan icazəsi vermədi və ya koordinatlar tapılmadı.',
                'is_violation' => true,
            ]);

            return [
                'success' => false,
                'message' => 'GPS məkan məlumatı alınmadı. Zəhmət olmasa telefonunuzda və brauzerdə məkan (lokasiya) icazəsini aktiv edin.',
                'reason' => 'location_permission_denied',
            ];
        }

        $distance = null;
        if ($branch->latitude && $branch->longitude) {
            $distance = $this->calculateDistance($userLat, $userLng, (float) $branch->latitude, (float) $branch->longitude);
            // Qlobal tənzimləmələrdən bütün filiallar üçün vahid radius götürülür
            $maxAllowedRadius = (int) AttendanceSetting::get('default_radius_meters', 100);

            if ($distance > $maxAllowedRadius) {
                $diff = round($distance - $maxAllowedRadius);
                $details = "İşçi filial mərkəzindən {$distance} metr məsafədədir. İcazə verilən maksimal radius: {$maxAllowedRadius} metr. ({$diff} metr artıqdır).";

                $this->logAttempt([
                    'worker_id' => $worker->id,
                    'worker_name' => $worker->name,
                    'branch_id' => $branch->id,
                    'branch_name' => $branch->name,
                    'date' => $today,
                    'type' => $actionType,
                    'scanned_at' => $now,
                    'latitude' => $userLat,
                    'longitude' => $userLng,
                    'distance_meters' => $distance,
                    'device_uuid' => $deviceUuid,
                    'device_model' => $deviceModel,
                    'ip_address' => $ipAddress,
                    'user_agent' => $userAgent,
                    'status' => 'rejected',
                    'rejection_reason' => 'out_of_range',
                    'rejection_details' => $details,
                    'is_violation' => true,
                ]);

                return [
                    'success' => false,
                    'message' => "Siz filial ərazisində deyilsiniz! Filialdan məsafəniz: {$distance} metr (İcazə verilən: {$maxAllowedRadius} m).",
                    'reason' => 'out_of_range',
                    'distance' => $distance,
                    'max_radius' => $maxAllowedRadius,
                ];
            }
        }

        // 5. Cihaz Limiti Yoxlanışı (1 Telefonda yalnız 1 işçi)
        if (AttendanceSetting::isSingleDeviceEnforced() && !empty($deviceUuid)) {
            $otherWorkerLog = AttendanceLog::where('date', $today)
                ->where('device_uuid', $deviceUuid)
                ->where('worker_id', '!=', $worker->id)
                ->where('status', 'success')
                ->latest('scanned_at')
                ->first();

            if ($otherWorkerLog) {
                $details = "Bu cihazdan bu gün artıq '{$otherWorkerLog->worker_name}' (ID: {$otherWorkerLog->worker_id}) üçün qeydiyyat aparılıb. '{$worker->name}' bu telefonla vura bilməz.";

                $this->logAttempt([
                    'worker_id' => $worker->id,
                    'worker_name' => $worker->name,
                    'branch_id' => $branch->id,
                    'branch_name' => $branch->name,
                    'date' => $today,
                    'type' => $actionType,
                    'scanned_at' => $now,
                    'latitude' => $userLat,
                    'longitude' => $userLng,
                    'distance_meters' => $distance,
                    'device_uuid' => $deviceUuid,
                    'device_model' => $deviceModel,
                    'ip_address' => $ipAddress,
                    'user_agent' => $userAgent,
                    'status' => 'rejected',
                    'rejection_reason' => 'device_already_used',
                    'rejection_details' => $details,
                    'is_violation' => true,
                ]);

                return [
                    'success' => false,
                    'message' => "Bu telefondan bu gün artıq başqa işçi ({$otherWorkerLog->worker_name}) üçün giriş-çıxış qeydə alınıb! Hər kəs yalnız öz telefonundan istifadə etməlidir.",
                    'reason' => 'device_already_used',
                ];
            }
        }

        // Gecikmə hesablanması (Əgər tənzimləmələrdə aktivdirsə)
        $lateMinutes = null;
        if ($actionType === 'check_in' && AttendanceSetting::get('track_late_arrivals', '0') === '1') {
            // Əvvəlcə filialın bugünkü iş qrafikini (həftəiçi və ya həftəsonu) götürürük, yoxdursa qlobal saatı
            $workStartTime = $branch->getTodayWorkStartTime() ?: AttendanceSetting::get('work_start_time', '09:00');
            $tolerance = (int) AttendanceSetting::get('late_tolerance_minutes', '15');
            $scheduledThreshold = Carbon::parse($today . ' ' . $workStartTime)->addMinutes($tolerance);

            if ($now->greaterThan($scheduledThreshold)) {
                $lateMinutes = (int) abs($now->diffInMinutes(Carbon::parse($today . ' ' . $workStartTime)));
            }
        }

        // 6. UĞURLU QEYDİYYAT!
        $log = AttendanceLog::create([
            'worker_id' => $worker->id,
            'worker_name' => $worker->name,
            'branch_id' => $branch->id,
            'branch_name' => $branch->name,
            'date' => $today,
            'type' => $actionType,
            'scanned_at' => $now,
            'latitude' => $userLat,
            'longitude' => $userLng,
            'distance_meters' => $distance,
            'late_minutes' => $lateMinutes,
            'device_uuid' => $deviceUuid,
            'device_model' => $deviceModel,
            'ip_address' => $ipAddress,
            'user_agent' => $userAgent,
            'status' => 'success',
            'rejection_reason' => null,
            'rejection_details' => null,
            'is_violation' => false,
        ]);

        // İşçiyə cihazı ilk dəfədirsə bağlayırıq
        if (empty($worker->registered_device_uuid) && !empty($deviceUuid)) {
            $worker->update(['registered_device_uuid' => $deviceUuid]);
        }

        $typeNameAz = $actionType === 'check_in' ? 'İşə Giriş' : 'İşdən Çıxış';

        return [
            'success' => true,
            'message' => "{$typeNameAz} uğurla qeydə alındı!",
            'time' => $now->format('H:i'),
            'date' => $now->format('d.m.Y'),
            'type' => $actionType,
            'type_name' => $typeNameAz,
            'worker_name' => $worker->name,
            'branch_name' => $branch->name,
            'distance' => $distance !== null ? "{$distance} m" : 'Təyin edilməyib',
            'next_action' => $worker->refresh()->next_action,
        ];
    }

    /**
     * Cəhdi bazaya qeyd edir.
     */
    protected function logAttempt(array $data): AttendanceLog
    {
        return AttendanceLog::create($data);
    }

    /**
     * PortalWebsite-dan filialları və işçiləri sinxronizasiya edir.
     */
    public function syncFromPortalWebsite(): array
    {
        $branchesCount = 0;
        $workersCount = 0;

        // İlkin tənzimləmələri təmin edirik
        AttendanceSetting::firstOrCreate(['key' => 'require_pin'], ['value' => '0', 'description' => 'PIN kod tələb olunsun (1/0)']);
        AttendanceSetting::firstOrCreate(['key' => 'default_radius_meters'], ['value' => '100', 'description' => 'Defolt filial radiusu (metr)']);
        AttendanceSetting::firstOrCreate(['key' => 'allow_device_reuse'], ['value' => '0', 'description' => 'Bir telefondan bir neçə işçiyə icazə (0=Xeyr, 1=Bəli)']);
        AttendanceSetting::firstOrCreate(['key' => 'work_start_time'], ['value' => '09:00', 'description' => 'İşin başlama saatı']);
        AttendanceSetting::firstOrCreate(['key' => 'late_tolerance_minutes'], ['value' => '15', 'description' => 'Gecikmə üçün icazə verilən dəqiqə']);
        AttendanceSetting::firstOrCreate(['key' => 'auto_checkout_enabled'], ['value' => '1', 'description' => 'Unudulmuş girişlər üçün təyin edilən saatda avtomatik çıxış (1/0)']);
        AttendanceSetting::firstOrCreate(['key' => 'auto_checkout_time'], ['value' => '06:00', 'description' => 'Avtomatik çıxışın icra saatı']);

        // 1. Əvvəlcə HTTP API vasitəsilə filialları çəkirik (qaimələr və mühasibatlıq kimi 100% işləyən etibarlı kanal)
        try {
            $portalService = app(\App\Services\PortalWebsiteService::class);
            $apiBranches = $portalService->getBranches();

            if (!empty($apiBranches)) {
                foreach ($apiBranches as $ab) {
                    $bId = $ab['id'] ?? null;
                    $bName = $ab['name'] ?? null;
                    if (!$bId || !$bName) continue;

                    $branch = AttendanceBranch::where('portal_branch_id', $bId)->first();
                    if (!$branch) {
                        $token = AttendanceBranch::generateUniqueToken();
                        AttendanceBranch::create([
                            'portal_branch_id' => $bId,
                            'name' => $bName,
                            'slug' => Str::slug($bName),
                            'qr_token' => $token,
                            'work_time_weekdays_start' => '14:00',
                            'work_time_weekdays_end' => '00:00',
                            'work_time_weekend_start' => '12:00',
                            'work_time_weekend_end' => '00:00',
                            'radius_meters' => 100,
                            'is_active' => true,
                        ]);
                        $branchesCount++;
                    } else {
                        $branch->update(['name' => $bName]);
                    }
                }
            }
        } catch (\Exception $e) {
            Log::warning("API branches sync warning: " . $e->getMessage());
        }

        // 2. Əgər birbaşa DB bağlantısı mümkündürsə, xəritə, koordinat və işçiləri zənginləşdiririk
        $dbError = null;
        try {
            $portalBranches = DB::connection('portal_website')->table('branches')
                ->leftJoin('branch_translations', function ($join) {
                    $join->on('branches.id', '=', 'branch_translations.branch_id')
                         ->where('branch_translations.locale', '=', 'az');
                })
                ->select([
                    'branches.id',
                    'branches.slug',
                    'branches.map',
                    'branches.is_open',
                    'branches.work_time_weekdays_start',
                    'branches.work_time_weekdays_end',
                    'branches.work_time_weekend_start',
                    'branches.work_time_weekend_end',
                    DB::raw('COALESCE(branch_translations.name, branches.slug) as branch_name'),
                ])
                ->get();

            foreach ($portalBranches as $pb) {
                $lat = null;
                $lng = null;

                if (!empty($pb->map)) {
                    if (preg_match('/!3d([0-9.]+)/', $pb->map, $mLat)) {
                        $lat = (float) $mLat[1];
                    }
                    if (preg_match('/!2d([0-9.]+)/', $pb->map, $mLng)) {
                        $lng = (float) $mLng[1];
                    }
                    if (($lat === null || $lng === null) && preg_match('/[?&]q=([0-9.]+),([0-9.]+)/', $pb->map, $mQ)) {
                        $lat = (float) $mQ[1];
                        $lng = (float) $mQ[2];
                    }
                }

                $branch = AttendanceBranch::where('portal_branch_id', $pb->id)->first();

                if (!$branch) {
                    $token = AttendanceBranch::generateUniqueToken();
                    AttendanceBranch::create([
                        'portal_branch_id' => $pb->id,
                        'name' => $pb->branch_name,
                        'slug' => $pb->slug ?: Str::slug($pb->branch_name),
                        'qr_token' => $token,
                        'map_url' => $pb->map,
                        'latitude' => $lat,
                        'longitude' => $lng,
                        'work_time_weekdays_start' => $pb->work_time_weekdays_start,
                        'work_time_weekdays_end' => $pb->work_time_weekdays_end,
                        'work_time_weekend_start' => $pb->work_time_weekend_start,
                        'work_time_weekend_end' => $pb->work_time_weekend_end,
                        'radius_meters' => 100,
                        'is_active' => true,
                    ]);
                    $branchesCount++;
                } else {
                    $updates = [
                        'name' => $pb->branch_name,
                        'work_time_weekdays_start' => $pb->work_time_weekdays_start,
                        'work_time_weekdays_end' => $pb->work_time_weekdays_end,
                        'work_time_weekend_start' => $pb->work_time_weekend_start,
                        'work_time_weekend_end' => $pb->work_time_weekend_end,
                    ];
                    if (!empty($pb->map)) {
                        $updates['map_url'] = $pb->map;
                    }
                    if (empty($branch->latitude) && $lat) {
                        $updates['latitude'] = $lat;
                    }
                    if (empty($branch->longitude) && $lng) {
                        $updates['longitude'] = $lng;
                    }
                    $branch->update($updates);
                }
            }

            // 3. İşçiləri çəkirik və sinxronlaşdırırıq
            $activePortalWorkers = DB::connection('portal_website')->table('workers')
                ->where('status', 'active')
                ->get();

            $activePortalWorkerIds = $activePortalWorkers->pluck('id')->all();
            $branchesMap = AttendanceBranch::pluck('id', 'portal_branch_id');

            foreach ($activePortalWorkers as $pw) {
                $attBranchId = $branchesMap[$pw->branch_id] ?? null;

                // Defolt PIN: telefonun son 4 rəqəmi, yoxdursa '1234'
                $defaultPin = '1234';
                if (!empty($pw->phone)) {
                    $digitsOnly = preg_replace('/\D/', '', $pw->phone);
                    if (strlen($digitsOnly) >= 4) {
                        $defaultPin = substr($digitsOnly, -4);
                    }
                }

                $worker = AttendanceWorker::where('portal_worker_id', $pw->id)->first();

                if (!$worker) {
                    AttendanceWorker::create([
                        'portal_worker_id' => $pw->id,
                        'portal_branch_id' => $pw->branch_id,
                        'attendance_branch_id' => $attBranchId,
                        'name' => $pw->name,
                        'phone' => $pw->phone,
                        'pin_code' => $defaultPin,
                        'is_active' => true,
                    ]);
                    $workersCount++;
                } else {
                    $worker->update([
                        'name' => $pw->name,
                        'phone' => $pw->phone,
                        'portal_branch_id' => $pw->branch_id,
                        'attendance_branch_id' => $attBranchId,
                    ]);
                }
            }

            // 4. Portal Website-da aktiv olmayan (archived, inactive və ya silinmiş) işçiləri ERP-də avtomatik DEAKTİV edirik!
            $deactivatedCount = AttendanceWorker::whereNotNull('portal_worker_id')
                ->whereNotIn('portal_worker_id', $activePortalWorkerIds)
                ->where('is_active', true)
                ->update(['is_active' => false]);
        } catch (\Exception $e) {
            $dbError = $e->getMessage();
            Log::warning("PortalWebsite DB sync xətası (API məlumatları saxlanıldı): " . $dbError);
        }

        $totalBranches = AttendanceBranch::count();
        $totalWorkers = AttendanceWorker::count();
        $activeWorkers = AttendanceWorker::where('is_active', true)->count();

        if ($totalBranches > 0) {
            $message = "Sinxronizasiya tamamlandı! Baza üzrə cəmi {$totalBranches} filial və {$activeWorkers} aktiv işçi mövcuddur.";
            if ($workersCount > 0) {
                $message .= " {$workersCount} yeni işçi əlavə edildi.";
            }
            if (!empty($deactivatedCount) && $deactivatedCount > 0) {
                $message .= " {$deactivatedCount} işçi deaktiv edildi (Portal-da aktiv olmadığı üçün).";
            } elseif ($dbError) {
                $message .= " (Filiallar API vasitəsilə uğurla çəkildi).";
            }

            return [
                'success' => true,
                'message' => $message,
                'branches_synced' => $totalBranches,
                'workers_synced' => $totalWorkers,
                'active_workers' => $activeWorkers,
                'deactivated_workers' => $deactivatedCount ?? 0,
                'db_error' => $dbError,
            ];
        }

        return [
            'success' => false,
            'message' => 'Sinxronizasiya xətası: ' . ($dbError ?: 'Filiallar tapılmadı.'),
        ];
    }

    /**
     * Unudulmuş girişləri təyin olunmuş saatda (məs: 06:00) avtomatik çıxış edir.
     * Həm Cron (Artisan command), həm də real vaxtda skan və ya jurnal açıldıqda icra olunur.
     */
    public function performAutoCheckout(): int
    {
        if (!AttendanceSetting::isAutoCheckoutEnabled()) {
            return 0;
        }

        $autoCheckoutTime = AttendanceSetting::getAutoCheckoutTime();
        $now = Carbon::now();
        $closedCount = 0;

        // Bütün işçiləri yoxlayırıq (açıq qalmış girişləri bağlamaq üçün)
        $workers = AttendanceWorker::all();

        foreach ($workers as $worker) {
            // İşçinin ən son uğurlu qeydiyyatı
            $lastLog = AttendanceLog::where('worker_id', $worker->id)
                ->where('status', 'success')
                ->latest('scanned_at')
                ->first();

            // Əgər ən son qeydiyyat yoxdursa və ya artıq çıxış edibsə, keçirik
            if (!$lastLog || $lastLog->type !== 'check_in') {
                continue;
            }

            // Giriş vaxtı
            $checkInTime = Carbon::parse($lastLog->scanned_at);
            $sameDayCutoff = $checkInTime->copy()->setTimeFromTimeString($autoCheckoutTime);

            // Əgər giriş saatı 06:00-dan sonra olubsa (məs: 09:00), avtomatik çıxış növbəti günün 06:00-dadır.
            // Əgər gecə növbəsi olub 06:00-dan əvvəl daxil olubsa (məs: 02:00), elə həmin günün 06:00-dadır.
            if ($checkInTime->greaterThanOrEqualTo($sameDayCutoff)) {
                $targetCheckoutAt = $sameDayCutoff->copy()->addDay();
            } else {
                $targetCheckoutAt = $sameDayCutoff->copy();
            }

            // Əgər cari vaxt bu hədəf çıxış saatına çatıbsa və ya keçibsə:
            if ($now->greaterThanOrEqualTo($targetCheckoutAt)) {
                // Təkrar eyni qeydin düşməməsi üçün yoxlayırıq
                $exists = AttendanceLog::where('worker_id', $worker->id)
                    ->where('type', 'check_out')
                    ->where('date', $targetCheckoutAt->toDateString())
                    ->where('scanned_at', $targetCheckoutAt)
                    ->exists();

                if (!$exists) {
                    AttendanceLog::create([
                        'worker_id' => $worker->id,
                        'worker_name' => $worker->name,
                        'branch_id' => $lastLog->branch_id,
                        'branch_name' => $lastLog->branch_name,
                        'date' => $targetCheckoutAt->toDateString(),
                        'type' => 'check_out',
                        'scanned_at' => $targetCheckoutAt,
                        'latitude' => null,
                        'longitude' => null,
                        'distance_meters' => null,
                        'late_minutes' => null,
                        'device_uuid' => null,
                        'device_model' => 'Avtomatik Sistem',
                        'ip_address' => null,
                        'user_agent' => 'System Auto-Checkout Daemon',
                        'status' => 'success',
                        'rejection_reason' => 'auto_checkout',
                        'rejection_details' => "Avtomatik Sistem Çıxışı — Unudulmuş çıxış",
                        'is_violation' => false,
                    ]);

                    $closedCount++;
                }
            }
        }

        if ($closedCount > 0) {
            Log::info("Attendance Auto-Checkout: {$closedCount} işçi üçün sistem tərəfindən avtomatik çıxış (logout) qeydə alındı.");
        }

        return $closedCount;
    }
}
