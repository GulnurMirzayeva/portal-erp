<?php

namespace App\Http\Controllers;

use App\Models\AttendanceBranch;
use App\Models\AttendanceLog;
use App\Models\AttendanceSetting;
use App\Models\AttendanceWorker;
use App\Services\AttendanceService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Str;
use SimpleSoftwareIO\QrCode\Facades\QrCode;

class AttendanceScanController extends Controller
{
    public function __construct(
        protected AttendanceService $attendanceService
    ) {}

    /**
     * İşçinin mobil telefonunda açılan skan səhifəsi.
     */
    public function showScanPage(string $token)
    {
        $branch = AttendanceBranch::where('qr_token', $token)
            ->where('is_active', true)
            ->firstOrFail();

        // Filialın aktiv işçiləri
        $workers = AttendanceWorker::where('attendance_branch_id', $branch->id)
            ->where('is_active', true)
            ->orderBy('name')
            ->get();

        // Əgər filialın xüsusi işçisi yoxdursa və ya ümumi siyahı lazımdırsa
        $allWorkers = AttendanceWorker::where('is_active', true)->orderBy('name')->get();

        $isPinRequired = AttendanceSetting::isPinRequired();
        $isSingleDevice = AttendanceSetting::isSingleDeviceEnforced();

        return view('attendance.scan', compact('branch', 'workers', 'allWorkers', 'isPinRequired', 'isSingleDevice'));
    }

    /**
     * İşçinin giriş/çıxış sorğusunu təsdiqləyir (AJAX).
     */
    public function submitAttendance(Request $request, string $token): JsonResponse
    {
        $request->validate([
            'worker_id' => 'required|integer',
            'latitude' => 'nullable|numeric',
            'longitude' => 'nullable|numeric',
            'pin_code' => 'nullable|string|max:10',
            'device_uuid' => 'nullable|string|max:100',
            'device_model' => 'nullable|string|max:150',
            'type' => 'nullable|in:check_in,check_out',
        ]);

        $ip = $request->ip();
        $userAgent = $request->userAgent();

        $result = $this->attendanceService->processAttendance(
            branchToken: $token,
            workerId: (int) $request->input('worker_id'),
            pinCode: $request->input('pin_code'),
            userLat: $request->filled('latitude') ? (float) $request->input('latitude') : null,
            userLng: $request->filled('longitude') ? (float) $request->input('longitude') : null,
            deviceUuid: $request->input('device_uuid'),
            deviceModel: $request->input('device_model'),
            ipAddress: $ip,
            userAgent: $userAgent,
            forceType: $request->input('type')
        );

        return response()->json($result, $result['success'] ? 200 : 422);
    }

    /**
     * Filialın QR kodunun SVG/PNG təsvirini qaytarır.
     */
    public function qrImage(string $token): Response
    {
        $branch = AttendanceBranch::where('qr_token', $token)->firstOrFail();
        $scanUrl = route('attendance.scan', ['token' => $branch->qr_token]);

        $svg = (string) QrCode::format('svg')
            ->size(350)
            ->margin(1)
            ->generate($scanUrl);

        return response($svg, 200, [
            'Content-Type' => 'image/svg+xml',
            'Cache-Control' => 'no-cache, private',
        ]);
    }

    /**
     * Filialın QR kodunu fayl olaraq yükləmə.
     */
    public function qrDownload(string $token): Response
    {
        $branch = AttendanceBranch::where('qr_token', $token)->firstOrFail();
        $scanUrl = route('attendance.scan', ['token' => $branch->qr_token]);

        $svg = (string) QrCode::format('svg')
            ->size(600)
            ->margin(2)
            ->generate($scanUrl);

        $fileName = 'QR_' . Str::slug($branch->name) . '.svg';

        return response($svg, 200, [
            'Content-Type' => 'image/svg+xml',
            'Content-Disposition' => "attachment; filename=\"{$fileName}\"",
        ]);
    }

    /**
     * QR print yönləndirməsi (artıq yalnız birbaşa sadə QR endirilir).
     */
    public function qrPrint(string $token)
    {
        return $this->qrDownload($token);
    }
}
