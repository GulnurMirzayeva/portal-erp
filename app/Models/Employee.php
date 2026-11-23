<?php

namespace App\Models;

use App\Services\WorkerTypeService;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Employee extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'portal_worker_id',
        'first_name',
        'last_name',
        'full_name',
        'tabel_display_name',
        'phone',
        'pin_code',
        'gender',
        'is_intern',
        'status',
        'hire_date',
        'dismissal_date',
        'requires_qr',
        'registered_device_uuid',
        'note',
    ];

    protected function casts(): array
    {
        return [
            'is_intern' => 'boolean',
            'requires_qr' => 'boolean',
            'hire_date' => 'date',
            'dismissal_date' => 'date',
        ];
    }

    /**
     * Vəzifələr (Çoxlu vəzifə: məs. Administrator + Resepşn)
     */
    public function workerTypes(): BelongsToMany
    {
        return $this->belongsToMany(WorkerType::class, 'employee_worker_types')
            ->withPivot('is_primary')
            ->withTimestamps()
            ->orderByPivot('is_primary', 'desc')
            ->orderBy('tabel_sort_order');
    }

    /**
     * Əsas vəzifə
     */
    public function primaryWorkerType(): ?WorkerType
    {
        return $this->workerTypes()->wherePivot('is_primary', true)->first()
            ?: $this->workerTypes()->first();
    }

    /**
     * Filiallar (1 əsas filial + Kuratorlar üçün əlavə idarə etdiyi filiallar)
     */
    public function branches(): BelongsToMany
    {
        return $this->belongsToMany(AttendanceBranch::class, 'employee_branches', 'employee_id', 'attendance_branch_id')
            ->withPivot(['portal_branch_id', 'is_primary'])
            ->withTimestamps()
            ->orderByPivot('is_primary', 'desc');
    }

    /**
     * Əsas filial
     */
    public function primaryBranch(): ?AttendanceBranch
    {
        return $this->branches()->wherePivot('is_primary', true)->first()
            ?: $this->branches()->first();
    }

    /**
     * İdarə etdiyi əlavə filiallar (Kurator üçün)
     */
    public function managedBranches(): BelongsToMany
    {
        return $this->branches()->wherePivot('is_primary', false);
    }

    /**
     * Baxdığı oyunlar (Aktyor / Oyun rolları üçün)
     */
    public function games(): HasMany
    {
        return $this->hasMany(EmployeeGame::class, 'employee_id');
    }

    /**
     * Status və vəzifə keçid tarixçələri (Stajorluqdan daimiyə və s.)
     */
    public function statusHistories(): HasMany
    {
        return $this->hasMany(EmployeeStatusHistory::class, 'employee_id')->latest('effective_date');
    }

    /**
     * Tabel üçün formatlanmış qısa etiket (məs: Əliyev / akt, Səlimova / staj-res, adm-res)
     */
    public function getTabelRoleLabelAttribute(): string
    {
        $types = $this->relationLoaded('workerTypes') ? $this->workerTypes : $this->workerTypes()->get();
        return app(WorkerTypeService::class)->composeTabelLabel(
            $types,
            $this->gender,
            $this->is_intern,
            false
        );
    }

    /**
     * Tabeldə görünən ad (Soyad və ya Qısa ad)
     */
    public function getTabelNameAttribute(): string
    {
        if (!empty($this->tabel_display_name)) {
            return $this->tabel_display_name;
        }

        if (!empty($this->last_name)) {
            return $this->last_name;
        }

        return $this->full_name;
    }

    /**
     * Cinsiyyət adı (Azərbaycan dilində)
     */
    public function getGenderLabelAttribute(): string
    {
        return $this->gender === 'female' ? 'Qadın' : 'Kişi';
    }

    /**
     * Status adı (Azərbaycan dilində)
     */
    public function getStatusLabelAttribute(): string
    {
        return match ($this->status) {
            'active' => 'Aktiv',
            'inactive' => 'Deaktiv',
            'dismissed' => 'İşdən çıxıb',
            default => $this->status,
        };
    }

    /**
     * İşçinin hal-hazırda açıq olan QR giriş logunu qaytarır (AttendanceWorker və ya cihaz vasitəsilə)
     */
    public function currentCheckInLog(): ?AttendanceLog
    {
        if (empty($this->registered_device_uuid) && empty($this->phone)) {
            return null;
        }

        // AttendanceWorker ilə əlaqəli son logu tapırıq
        $attWorker = AttendanceWorker::where(function ($q) {
            if ($this->phone) $q->where('phone', $this->phone);
            if ($this->registered_device_uuid) $q->orWhere('registered_device_uuid', $this->registered_device_uuid);
            if ($this->portal_worker_id) $q->orWhere('portal_worker_id', $this->portal_worker_id);
        })->first();

        return $attWorker?->currentCheckInLog();
    }

    /**
     * Növbəti gözlənilən QR hərəkəti (check_in yoxsa check_out)
     */
    public function getNextActionAttribute(): string
    {
        return $this->currentCheckInLog() ? 'check_out' : 'check_in';
    }
}
