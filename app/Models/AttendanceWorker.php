<?php

namespace App\Models;

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
            return 'check_in';
        }

        return $lastLog->type === 'check_in' ? 'check_out' : 'check_in';
    }
}
