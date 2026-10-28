<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

class AttendanceBranch extends Model
{
    protected $fillable = [
        'portal_branch_id',
        'name',
        'slug',
        'qr_token',
        'latitude',
        'longitude',
        'map_url',
        'work_time_weekdays_start',
        'work_time_weekdays_end',
        'work_time_weekend_start',
        'work_time_weekend_end',
        'radius_meters',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'latitude' => 'float',
            'longitude' => 'float',
            'radius_meters' => 'integer',
            'is_active' => 'boolean',
        ];
    }

    /**
     * Cari gün üçün (həftəiçi və ya həftəsonu) işin başlama saatını qaytarır.
     */
    public function getTodayWorkStartTime(): ?string
    {
        $isWeekend = \Carbon\Carbon::now()->isWeekend();

        if ($isWeekend && !empty($this->work_time_weekend_start)) {
            return $this->work_time_weekend_start;
        }

        return $this->work_time_weekdays_start;
    }

    /**
     * Cari gün üçün (həftəiçi və ya həftəsonu) işin bitmə saatını qaytarır.
     */
    public function getTodayWorkEndTime(): ?string
    {
        $isWeekend = \Carbon\Carbon::now()->isWeekend();

        if ($isWeekend && !empty($this->work_time_weekend_end)) {
            return $this->work_time_weekend_end;
        }

        return $this->work_time_weekdays_end;
    }

    public function workers(): HasMany
    {
        return $this->hasMany(AttendanceWorker::class, 'attendance_branch_id');
    }

    public function activeWorkers(): HasMany
    {
        return $this->hasMany(AttendanceWorker::class, 'attendance_branch_id')->where('is_active', true);
    }

    public function logs(): HasMany
    {
        return $this->hasMany(AttendanceLog::class, 'branch_id');
    }

    /**
     * Unikal QR token generasiya edir.
     */
    public static function generateUniqueToken(): string
    {
        do {
            $token = Str::random(24);
        } while (static::where('qr_token', $token)->exists());

        return $token;
    }

    /**
     * Skan üçün URL-i qaytarır.
     */
    public function getScanUrlAttribute(): string
    {
        return route('attendance.scan', ['token' => $this->qr_token]);
    }

    /**
     * Tənzimləmələrdəki vahid filial radiusunu qaytarır.
     */
    public function getRadiusMetersAttribute($value): int
    {
        return (int) AttendanceSetting::get('default_radius_meters', $value ?: 100);
    }
}
