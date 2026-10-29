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
     * İşçinin hal-hazırda açıq olan (çıxışı edilməmiş) giriş logunu qaytarır.
     */
    public function currentCheckInLog(): ?AttendanceLog
    {
        $lastLog = $this->logs()
            ->where('status', 'success')
            ->latest('scanned_at')
            ->first();

        if ($lastLog && $lastLog->type === 'check_in') {
            // Əgər avtomatik çıxış aktivdirsə və vaxtı keçibsə, açıq sayılmır
            if (AttendanceSetting::isAutoCheckoutEnabled()) {
                $autoCheckoutTime = AttendanceSetting::getAutoCheckoutTime();
                $checkInTime = Carbon::parse($lastLog->scanned_at);
                $sameDayCutoff = $checkInTime->copy()->setTimeFromTimeString($autoCheckoutTime);
                $targetCheckoutAt = $checkInTime->greaterThanOrEqualTo($sameDayCutoff)
                    ? $sameDayCutoff->copy()->addDay()
                    : $sameDayCutoff->copy();

                if (Carbon::now()->greaterThanOrEqualTo($targetCheckoutAt)) {
                    return null;
                }
            }

            return $lastLog;
        }

        return null;
    }

    /**
     * Növbəti gözlənilən hərəkət (check_in yoxsa check_out).
     */
    public function getNextActionAttribute(): string
    {
        return $this->currentCheckInLog() ? 'check_out' : 'check_in';
    }
}
