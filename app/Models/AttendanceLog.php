<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AttendanceLog extends Model
{
    protected $fillable = [
        'worker_id',
        'worker_name',
        'branch_id',
        'branch_name',
        'date',
        'type',
        'scanned_at',
        'latitude',
        'longitude',
        'distance_meters',
        'late_minutes',
        'device_uuid',
        'device_model',
        'ip_address',
        'user_agent',
        'status',
        'rejection_reason',
        'rejection_details',
        'is_violation',
    ];

    protected function casts(): array
    {
        return [
            'date' => 'date',
            'scanned_at' => 'datetime',
            'latitude' => 'float',
            'longitude' => 'float',
            'distance_meters' => 'float',
            'late_minutes' => 'integer',
            'is_violation' => 'boolean',
        ];
    }

    public function worker(): BelongsTo
    {
        return $this->belongsTo(AttendanceWorker::class, 'worker_id');
    }

    public function branch(): BelongsTo
    {
        return $this->belongsTo(AttendanceBranch::class, 'branch_id');
    }

    /**
     * İnsan tərəfindən oxuna bilən imtina səbəbi.
     */
    public function getFormattedRejectionReasonAttribute(): string
    {
        return match ($this->rejection_reason) {
            'out_of_range' => 'Məsafə limiti aşıldı (Filialdan kənarda)',
            'device_already_used' => 'Cihaz limiti: Bu telefondan bu gün başqa işçi vurub',
            'wrong_pin' => 'Yanlış PIN kod daxil edildi',
            'pin_required' => 'PIN kod daxil edilməyib',
            'device_mismatch' => 'Yad cihaz: İşçi başqa telefondan cəhd etdi',
            'location_permission_denied' => 'GPS məkan icazəsi verilməyib',
            'inactive_worker' => 'İşçi statusu deaktivdir',
            'already_checked_in' => 'Artıq giriş edilib',
            'already_checked_out' => 'Artıq çıxış edilib',
            default => $this->rejection_reason ?? 'Bilinməyən xəta',
        };
    }

    /**
     * Qeydiyyatın avtomatik sistem çıxışı olub-olmadığını yoxlayır.
     */
    public function isAutoCheckout(): bool
    {
        return $this->rejection_reason === 'auto_checkout'
            || ($this->rejection_details && str_contains($this->rejection_details, 'Avtomatik'))
            || ($this->device_model && str_contains($this->device_model, 'Avtomatik'));
    }
}
