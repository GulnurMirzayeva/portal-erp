<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class AttendanceWorker extends Model
{
    protected $fillable = [
        'portal_worker_id',
        'portal_branch_id',
        'attendance_branch_id',
        'name',
        'phone',
        'pin_code',
        'registered_device_uuid',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
        ];
    }

    public function branch(): BelongsTo
    {
        return $this->belongsTo(AttendanceBranch::class, 'attendance_branch_id');
    }

    public function logs(): HasMany
    {
        return $this->hasMany(AttendanceLog::class, 'worker_id');
    }

    /**
     * İşçinin bu gün üçün son qeydiyyatını qaytarır.
     */
    public function todayLastLog()
    {
        return $this->logs()
            ->where('date', date('Y-m-d'))
            ->where('status', 'success')
            ->latest('scanned_at')
            ->first();
    }

    /**
     * Növbəti gözlənilən hərəkət (check_in yoxsa check_out).
     */
    public function getNextActionAttribute(): string
    {
        $lastLog = $this->todayLastLog();

        if (!$lastLog) {
            // Əgər bu gün hələ log yoxdursa, amma əvvəldən açıq qalmış qeydiyyat varsa:
            $latestOverall = $this->logs()
                ->where('status', 'success')
                ->latest('scanned_at')
                ->first();

            if ($latestOverall && $latestOverall->type === 'check_in') {
                if (AttendanceSetting::isAutoCheckoutEnabled()) {
                    $autoCheckoutTime = AttendanceSetting::getAutoCheckoutTime();
                    $checkInTime = Carbon::parse($latestOverall->scanned_at);
                    $sameDayCutoff = $checkInTime->copy()->setTimeFromTimeString($autoCheckoutTime);
                    $targetCheckoutAt = $checkInTime->greaterThanOrEqualTo($sameDayCutoff)
                        ? $sameDayCutoff->copy()->addDay()
                        : $sameDayCutoff->copy();

                    if (Carbon::now()->greaterThanOrEqualTo($targetCheckoutAt)) {
                        return 'check_in';
                    }
                }
                return 'check_out';
            }

            return 'check_in';
        }

        return $lastLog->type === 'check_in' ? 'check_out' : 'check_in';
    }
}
