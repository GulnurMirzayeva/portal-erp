<?php

namespace App\Services;

use App\Models\AttendanceBranch;
use App\Models\AttendanceWorker;
use App\Models\Employee;
use App\Models\EmployeeGame;
use App\Models\EmployeeStatusHistory;
use App\Models\WorkerType;
use Carbon\Carbon;
use Exception;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class EmployeeService
{
    /**
     * İşçiləri süzgəcdən keçirən sorğu qurucusu (Axtarış, filial, vəzifə, status, stajorluq).
     */
    public function query(array $filters = []): Builder
    {
        $query = Employee::with(['workerTypes', 'branches', 'games'])
            ->latest('id');

        if (!empty($filters['search'])) {
            $search = trim($filters['search']);
            $query->where(function ($q) use ($search) {
                $q->where('full_name', 'like', "%{$search}%")
                  ->orWhere('first_name', 'like', "%{$search}%")
                  ->orWhere('last_name', 'like', "%{$search}%")
                  ->orWhere('phone', 'like', "%{$search}%")
                  ->orWhere('pin_code', 'like', "%{$search}%");
            });
        }

        if (!empty($filters['branch_id']) && $filters['branch_id'] !== 'all') {
            $branchId = (int) $filters['branch_id'];
            $query->whereHas('branches', function ($q) use ($branchId) {
                $q->where('attendance_branches.id', $branchId);
            });
        }

        if (!empty($filters['worker_type_id']) && $filters['worker_type_id'] !== 'all') {
            $typeId = (int) $filters['worker_type_id'];
            $query->whereHas('workerTypes', function ($q) use ($typeId) {
                $q->where('worker_types.id', $typeId);
            });
        }

        if (isset($filters['is_intern']) && $filters['is_intern'] !== '' && $filters['is_intern'] !== 'all') {
            $query->where('is_intern', (bool) $filters['is_intern']);
        }

        if (!empty($filters['status']) && $filters['status'] !== 'all') {
            $query->where('status', $filters['status']);
        }

        if (!empty($filters['gender']) && $filters['gender'] !== 'all') {
            $query->where('gender', $filters['gender']);
        }

        return $query;
    }

    /**
     * Yeni işçi yaradır və əlaqəli vəzifələri, filialları, oyunları təyin edir.
     */
    public function create(array $data, ?int $createdByUserId = null): Employee
    {
        return DB::transaction(function () use ($data, $createdByUserId) {
            $isIntern = !empty($data['is_intern']);

            // Stajorluq qaydası yoxlanışı: İdarəedici və ya stajor qəbul etməyən vəzifələr stajor kimi seçilə bilməz
            if ($isIntern && !empty($data['worker_type_ids'])) {
                $invalidTypes = WorkerType::whereIn('id', $data['worker_type_ids'])
                    ->where('can_have_intern', false)
                    ->pluck('name')
                    ->all();

                if (!empty($invalidTypes)) {
                    throw new Exception("Seçilmiş vəzifələrdə stajorluq icazəsi yoxdur: " . implode(', ', $invalidTypes));
                }
            }

            $fullName = trim($data['full_name'] ?? '');
            $parsedNames = $this->parseFirstAndLastName($fullName, $data['first_name'] ?? null, $data['last_name'] ?? null);

            $employee = Employee::create([
                'portal_worker_id' => $data['portal_worker_id'] ?? null,
                'first_name' => $parsedNames['first_name'],
                'last_name' => $parsedNames['last_name'],
                'full_name' => $parsedNames['full_name'],
                'tabel_display_name' => !empty($data['tabel_display_name']) ? trim($data['tabel_display_name']) : $parsedNames['last_name'],
                'phone' => !empty($data['phone']) ? trim($data['phone']) : null,
                'pin_code' => !empty($data['pin_code']) ? trim($data['pin_code']) : '1234',
                'gender' => in_array($data['gender'] ?? '', ['male', 'female'], true) ? $data['gender'] : 'male',
                'is_intern' => $isIntern,
                'status' => in_array($data['status'] ?? '', ['active', 'inactive', 'dismissed'], true) ? $data['status'] : 'active',
                'hire_date' => !empty($data['hire_date']) ? Carbon::parse($data['hire_date']) : Carbon::today(),
                'dismissal_date' => !empty($data['dismissal_date']) ? Carbon::parse($data['dismissal_date']) : null,
                'requires_qr' => isset($data['requires_qr']) ? (bool) $data['requires_qr'] : true,
                'registered_device_uuid' => $data['registered_device_uuid'] ?? null,
                'note' => $data['note'] ?? null,
            ]);

            // Vəzifələri bağlayırıq
            $this->syncWorkerTypes($employee, $data['worker_type_ids'] ?? [], $data['primary_worker_type_id'] ?? null);

            // Filialları bağlayırıq (Əsas filial + Kurator üçün idarə etdiyi əlavə filiallar)
            $this->syncBranches($employee, $data['primary_branch_id'] ?? null, $data['managed_branch_ids'] ?? []);

            // Oyunları bağlayırıq
            $this->syncGames($employee, $data['game_ids'] ?? []);

            // Tarixçəyə ilk qeyd
            EmployeeStatusHistory::create([
                'employee_id' => $employee->id,
                'event_type' => 'created',
                'old_value' => null,
                'new_value' => [
                    'status' => $employee->status,
                    'is_intern' => $employee->is_intern,
                    'worker_types' => $employee->workerTypes()->pluck('name')->all(),
                    'primary_branch' => $employee->primaryBranch()?->name,
                ],
                'effective_date' => $employee->hire_date ?: Carbon::today(),
                'note' => 'İşçi kartı yaradıldı',
                'created_by_user_id' => $createdByUserId,
            ]);

            // QR AttendanceWorker cədvəli ilə sinxronlaşdırırıq
            $this->syncToAttendanceWorker($employee);

            return $employee;
        });
    }

    /**
     * Mövcud işçini yeniləyir və dəyişiklikləri tarixçədə qeyd edir.
     */
    public function update(Employee $employee, array $data, ?int $updatedByUserId = null): Employee
    {
        return DB::transaction(function () use ($employee, $data, $updatedByUserId) {
            $oldIsIntern = $employee->is_intern;
            $newIsIntern = !empty($data['is_intern']);

            // Stajorluq qaydası yoxlanışı
            if ($newIsIntern && !empty($data['worker_type_ids'])) {
                $invalidTypes = WorkerType::whereIn('id', $data['worker_type_ids'])
                    ->where('can_have_intern', false)
                    ->pluck('name')
                    ->all();

                if (!empty($invalidTypes)) {
                    throw new Exception("Seçilmiş vəzifələrdə stajorluq icazəsi yoxdur: " . implode(', ', $invalidTypes));
                }
            }

            $fullName = trim($data['full_name'] ?? $employee->full_name);
            $parsedNames = $this->parseFirstAndLastName($fullName, $data['first_name'] ?? null, $data['last_name'] ?? null);

            $oldValues = [
                'status' => $employee->status,
                'is_intern' => $employee->is_intern,
                'worker_types' => $employee->workerTypes()->pluck('name')->all(),
                'primary_branch' => $employee->primaryBranch()?->name,
            ];

            $employee->update([
                'first_name' => $parsedNames['first_name'],
                'last_name' => $parsedNames['last_name'],
                'full_name' => $parsedNames['full_name'],
                'tabel_display_name' => !empty($data['tabel_display_name']) ? trim($data['tabel_display_name']) : $parsedNames['last_name'],
                'phone' => !empty($data['phone']) ? trim($data['phone']) : null,
                'pin_code' => !empty($data['pin_code']) ? trim($data['pin_code']) : $employee->pin_code,
                'gender' => in_array($data['gender'] ?? '', ['male', 'female'], true) ? $data['gender'] : $employee->gender,
                'is_intern' => $newIsIntern,
                'status' => in_array($data['status'] ?? '', ['active', 'inactive', 'dismissed'], true) ? $data['status'] : $employee->status,
                'hire_date' => !empty($data['hire_date']) ? Carbon::parse($data['hire_date']) : $employee->hire_date,
                'dismissal_date' => !empty($data['dismissal_date']) ? Carbon::parse($data['dismissal_date']) : null,
                'requires_qr' => isset($data['requires_qr']) ? (bool) $data['requires_qr'] : $employee->requires_qr,
                'registered_device_uuid' => array_key_exists('registered_device_uuid', $data) ? $data['registered_device_uuid'] : $employee->registered_device_uuid,
                'note' => $data['note'] ?? $employee->note,
            ]);

            // Vəzifələri yeniləyirik
            if (isset($data['worker_type_ids'])) {
                $this->syncWorkerTypes($employee, $data['worker_type_ids'], $data['primary_worker_type_id'] ?? null);
            }

            // Filialları yeniləyirik
            if (array_key_exists('primary_branch_id', $data) || array_key_exists('managed_branch_ids', $data)) {
                $this->syncBranches($employee, $data['primary_branch_id'] ?? null, $data['managed_branch_ids'] ?? []);
            }

            // Oyunları yeniləyirik
            if (array_key_exists('game_ids', $data)) {
                $this->syncGames($employee, $data['game_ids'] ?? []);
            }

            // Stajorluqdan daimiyə keçid qeydiyyatı
            if ($oldIsIntern && !$newIsIntern) {
                EmployeeStatusHistory::create([
                    'employee_id' => $employee->id,
                    'event_type' => 'intern_promoted',
                    'old_value' => ['is_intern' => true],
                    'new_value' => ['is_intern' => false],
                    'effective_date' => !empty($data['promotion_effective_date']) ? Carbon::parse($data['promotion_effective_date']) : Carbon::today(),
                    'note' => $data['status_history_note'] ?? 'Stajorluq uğurla başa çatdı — daimi işçi statusuna keçirildi',
                    'created_by_user_id' => $updatedByUserId,
                ]);
            }

            // QR AttendanceWorker cədvəli ilə sinxronlaşdırırıq
            $this->syncToAttendanceWorker($employee);

            return $employee;
        });
    }

    /**
     * İşçini silir (Soft delete) və QR statusunu deaktiv edir.
     */
    public function delete(Employee $employee): bool
    {
        return DB::transaction(function () use ($employee) {
            $employee->update(['status' => 'dismissed']);
            $deleted = $employee->delete();

            // QR işçi cədvəlini də deaktiv edirik
            if ($employee->phone || $employee->portal_worker_id) {
                AttendanceWorker::where(function ($q) use ($employee) {
                    if ($employee->phone) $q->where('phone', $employee->phone);
                    if ($employee->portal_worker_id) $q->orWhere('portal_worker_id', $employee->portal_worker_id);
                })->update(['is_active' => false]);
            }

            return (bool) $deleted;
        });
    }

    /**
     * Vəzifələri işçiyə bağlayır (is_primary qeydi ilə).
     */
    public function syncWorkerTypes(Employee $employee, array $workerTypeIds, ?int $primaryId = null): void
    {
        $syncData = [];
        $typeIds = array_unique(array_filter(array_map('intval', $workerTypeIds)));

        if (empty($typeIds)) {
            $employee->workerTypes()->sync([]);
            return;
        }

        $primaryId = $primaryId && in_array($primaryId, $typeIds, true) ? $primaryId : reset($typeIds);

        foreach ($typeIds as $tId) {
            $syncData[$tId] = [
                'is_primary' => ($tId === $primaryId),
            ];
        }

        $employee->workerTypes()->sync($syncData);
    }

    /**
     * Filialları işçiyə bağlayır (Əsas filial + əlavə idarə etdiyi filiallar).
     */
    public function syncBranches(Employee $employee, ?int $primaryBranchId, array $managedBranchIds = []): void
    {
        $syncData = [];

        if ($primaryBranchId) {
            $pb = AttendanceBranch::find($primaryBranchId);
            if ($pb) {
                $syncData[$pb->id] = [
                    'portal_branch_id' => $pb->portal_branch_id,
                    'is_primary' => true,
                ];
            }
        }

        $managedIds = array_unique(array_filter(array_map('intval', $managedBranchIds)));
        foreach ($managedIds as $mId) {
            if ($mId === $primaryBranchId) continue;
            $mb = AttendanceBranch::find($mId);
            if ($mb) {
                $syncData[$mb->id] = [
                    'portal_branch_id' => $mb->portal_branch_id,
                    'is_primary' => false,
                ];
            }
        }

        $employee->branches()->sync($syncData);
    }

    /**
     * Baxdığı oyunları işçiyə bağlayır.
     */
    public function syncGames(Employee $employee, array $gameIds): void
    {
        $gameIds = array_unique(array_filter(array_map('intval', $gameIds)));
        $employee->games()->delete();

        if (empty($gameIds)) {
            return;
        }

        $allGames = app(PortalWebsiteService::class)->getAllGames();
        $gamesMap = collect($allGames)->keyBy('id');

        $rows = [];
        foreach ($gameIds as $gId) {
            $gName = $gamesMap->get($gId)['label'] ?? $gamesMap->get($gId)['name'] ?? "Oyun #{$gId}";
            $rows[] = [
                'employee_id' => $employee->id,
                'portal_game_id' => $gId,
                'game_name' => $gName,
                'created_at' => now(),
                'updated_at' => now(),
            ];
        }

        if (!empty($rows)) {
            EmployeeGame::insert($rows);
        }
    }

    /**
     * QR AttendanceWorker cədvəlini bu işçi ilə tam sinxron saxlayır.
     */
    public function syncToAttendanceWorker(Employee $employee): ?AttendanceWorker
    {
        $primaryBranch = $employee->primaryBranch();

        $attWorker = AttendanceWorker::where(function ($q) use ($employee) {
            if ($employee->portal_worker_id) {
                $q->where('portal_worker_id', $employee->portal_worker_id);
            }
            if ($employee->phone) {
                $q->orWhere('phone', $employee->phone);
            }
        })->first();

        $attributes = [
            'name' => $employee->full_name,
            'phone' => $employee->phone,
            'pin_code' => $employee->pin_code ?: '1234',
            'attendance_branch_id' => $primaryBranch?->id,
            'portal_branch_id' => $primaryBranch?->portal_branch_id,
            'registered_device_uuid' => $employee->registered_device_uuid,
            'is_active' => ($employee->status === 'active'),
        ];

        if ($employee->portal_worker_id) {
            $attributes['portal_worker_id'] = $employee->portal_worker_id;
        }

        if ($attWorker) {
            $attWorker->update($attributes);
            return $attWorker;
        }

        return AttendanceWorker::create($attributes);
    }

    /**
     * Mövcud sayt və QR işçilərini ERP-yə avtomatik və təhlükəsiz idxal edir.
     */
    public function importFromPortalWebsite(): array
    {
        $created = 0;
        $updated = 0;
        $skipped = 0;

        // Vəzifələr və Filiallar xəritəsini qururuq
        $typesByPortalId = WorkerType::whereNotNull('portal_worker_type_id')->get()->keyBy('portal_worker_type_id');
        $actorType = WorkerType::where('slug', 'aktyor')->first();
        $branchesByPortalId = AttendanceBranch::whereNotNull('portal_branch_id')->get()->keyBy('portal_branch_id');

        // Saytdan aktiv işçiləri çəkirik
        $portalWorkers = [];
        try {
            $portalWorkers = DB::connection('portal_website')->table('workers')
                ->where('status', 'active')
                ->get();
        } catch (Exception $e) {
            Log::warning("PortalWebsite workers çəkilərkən xəta: " . $e->getMessage());
        }

        // Əgər sayt bazası əlçatan deyilsə, mövcud AttendanceWorker cədvəlindən idxal edirik
        if (empty($portalWorkers) || count($portalWorkers) === 0) {
            $attWorkers = AttendanceWorker::where('is_active', true)->get();
            foreach ($attWorkers as $aw) {
                $emp = Employee::where('phone', $aw->phone)
                    ->orWhere('portal_worker_id', $aw->portal_worker_id)
                    ->first();

                $parsedNames = $this->parseFirstAndLastName($aw->name);

                if (!$emp) {
                    $emp = Employee::create([
                        'portal_worker_id' => $aw->portal_worker_id,
                        'first_name' => $parsedNames['first_name'],
                        'last_name' => $parsedNames['last_name'],
                        'full_name' => $parsedNames['full_name'],
                        'tabel_display_name' => $parsedNames['last_name'],
                        'phone' => $aw->phone,
                        'pin_code' => $aw->pin_code ?: '1234',
                        'gender' => 'male',
                        'is_intern' => false,
                        'status' => 'active',
                        'hire_date' => Carbon::today(),
                        'registered_device_uuid' => $aw->registered_device_uuid,
                    ]);
                    if ($aw->attendance_branch_id) {
                        $emp->branches()->sync([$aw->attendance_branch_id => ['is_primary' => true]]);
                    }
                    $created++;
                } else {
                    $emp->update([
                        'registered_device_uuid' => $aw->registered_device_uuid ?: $emp->registered_device_uuid,
                        'pin_code' => $aw->pin_code ?: $emp->pin_code,
                    ]);
                    $updated++;
                }
            }

            return [
                'source' => 'attendance_workers',
                'created' => $created,
                'updated' => $updated,
                'total_employees' => Employee::count(),
            ];
        }

        // Saytın worker_worker_type cədvəlini oxuyuruq
        $workerRolesMap = collect();
        try {
            $workerRolesMap = DB::connection('portal_website')->table('worker_worker_type')
                ->get()
                ->groupBy('worker_id');
        } catch (Exception $e) {
            Log::warning("PortalWebsite worker_worker_type çəkilərkən xəta: " . $e->getMessage());
        }

        foreach ($portalWorkers as $pw) {
            $emp = Employee::where('portal_worker_id', $pw->id)
                ->orWhere(function ($q) use ($pw) {
                    if (!empty($pw->phone)) {
                        $q->where('phone', $pw->phone);
                    }
                })
                ->first();

            $parsedNames = $this->parseFirstAndLastName($pw->name);

            // Vəzifələri müəyyən edirik
            $roles = $workerRolesMap->get($pw->id, collect());
            $matchedWorkerTypeIds = [];
            $isFemale = false;

            foreach ($roles as $r) {
                // Əgər köhnə vəzifə Aktrisa idisə (portal_worker_type_id = 2), cinsiyyəti qadın edirik və Aktyor tipinə bağlayırıq
                if ((int)$r->worker_type_id === 2) {
                    $isFemale = true;
                    if ($actorType) $matchedWorkerTypeIds[] = $actorType->id;
                    continue;
                }

                $wt = $typesByPortalId->get($r->worker_type_id);
                if ($wt) {
                    $matchedWorkerTypeIds[] = $wt->id;
                }
            }

            $matchedWorkerTypeIds = array_unique($matchedWorkerTypeIds);
            $branch = $branchesByPortalId->get($pw->branch_id);

            // Cihaz UUID və PIN AttendanceWorker-dən çəkirik
            $attWorker = AttendanceWorker::where('portal_worker_id', $pw->id)
                ->orWhere('phone', $pw->phone)
                ->first();

            $pin = $attWorker?->pin_code;
            if (empty($pin) && !empty($pw->phone)) {
                $digits = preg_replace('/\D/', '', $pw->phone);
                if (strlen($digits) >= 4) {
                    $pin = substr($digits, -4);
                }
            }
            if (empty($pin)) $pin = '1234';

            $data = [
                'portal_worker_id' => $pw->id,
                'first_name' => $parsedNames['first_name'],
                'last_name' => $parsedNames['last_name'],
                'full_name' => $parsedNames['full_name'],
                'tabel_display_name' => $parsedNames['last_name'],
                'phone' => $pw->phone,
                'pin_code' => $pin,
                'gender' => $isFemale ? 'female' : 'male',
                'is_intern' => false,
                'status' => 'active',
                'hire_date' => !empty($pw->hire_date) ? Carbon::parse($pw->hire_date) : Carbon::today(),
                'registered_device_uuid' => $attWorker?->registered_device_uuid,
            ];

            if (!$emp) {
                $emp = Employee::create($data);
                $created++;
            } else {
                $emp->update($data);
                $updated++;
            }

            // Vəzifələri bağlayırıq
            if (!empty($matchedWorkerTypeIds)) {
                $this->syncWorkerTypes($emp, $matchedWorkerTypeIds);
            }

            // Filialı bağlayırıq
            if ($branch) {
                $this->syncBranches($emp, $branch->id, []);
            }

            // AttendanceWorker-i də təzələyirik
            $this->syncToAttendanceWorker($emp);
        }

        return [
            'source' => 'portal_website',
            'created' => $created,
            'updated' => $updated,
            'total_employees' => Employee::count(),
        ];
    }

    /**
     * Tam adı Ad və Soyada ayırır.
     */
    protected function parseFirstAndLastName(string $fullName, ?string $explicitFirst = null, ?string $explicitLast = null): array
    {
        $fullName = trim($fullName);
        if ($explicitFirst && $explicitLast) {
            return [
                'first_name' => trim($explicitFirst),
                'last_name' => trim($explicitLast),
                'full_name' => trim($explicitFirst . ' ' . $explicitLast),
            ];
        }

        $parts = preg_split('/\s+/', $fullName);
        $first = $explicitFirst ?: ($parts[0] ?? '');
        $last = $explicitLast ?: (isset($parts[1]) ? implode(' ', array_slice($parts, 1)) : '');

        return [
            'first_name' => $first,
            'last_name' => $last,
            'full_name' => $fullName,
        ];
    }
}
