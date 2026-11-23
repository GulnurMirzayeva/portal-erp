<?php

namespace App\Http\Controllers;

use App\Models\AttendanceBranch;
use App\Models\Employee;
use App\Models\WorkerType;
use App\Services\EmployeeService;
use App\Services\PortalWebsiteService;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class EmployeeController extends Controller
{
    public function __construct(
        protected EmployeeService $employeeService
    ) {}

    /**
     * İşçilərin ümumi siyahısı və statistikası.
     */
    public function index(Request $request): View
    {
        $filters = [
            'search' => $request->input('search'),
            'branch_id' => $request->input('branch_id'),
            'worker_type_id' => $request->input('worker_type_id'),
            'is_intern' => $request->input('is_intern'),
            'status' => $request->input('status', 'all'),
            'gender' => $request->input('gender', 'all'),
        ];

        $employees = $this->employeeService->query($filters)->paginate(20)->withQueryString();

        // Statistika
        $stats = [
            'total' => Employee::count(),
            'active' => Employee::where('status', 'active')->count(),
            'interns' => Employee::where('status', 'active')->where('is_intern', true)->count(),
            'permanent' => Employee::where('status', 'active')->where('is_intern', false)->count(),
            'inactive' => Employee::where('status', '!=', 'active')->count(),
        ];

        $branches = AttendanceBranch::orderBy('name')->get();
        $workerTypes = WorkerType::where('is_active', true)->orderBy('tabel_sort_order')->get();

        return view('employees.index', compact(
            'employees',
            'stats',
            'branches',
            'workerTypes',
            'filters'
        ));
    }

    /**
     * Yeni işçi yaratma forması.
     */
    public function create(): View
    {
        $employee = new Employee([
            'status' => 'active',
            'gender' => 'male',
            'is_intern' => false,
            'requires_qr' => true,
            'pin_code' => '1234',
            'hire_date' => now()->toDateString(),
        ]);

        $branches = AttendanceBranch::where('is_active', true)->orderBy('name')->get();
        $workerTypes = WorkerType::where('is_active', true)->orderBy('tabel_sort_order')->get();
        $games = app(PortalWebsiteService::class)->getAllGames();

        return view('employees.create', compact('employee', 'branches', 'workerTypes', 'games'));
    }

    /**
     * Yeni işçini yadda saxlayır.
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'full_name' => 'required|string|max:255',
            'first_name' => 'nullable|string|max:100',
            'last_name' => 'nullable|string|max:100',
            'tabel_display_name' => 'nullable|string|max:100',
            'phone' => 'nullable|string|max:30',
            'pin_code' => 'nullable|string|max:10',
            'gender' => 'required|in:male,female',
            'is_intern' => 'nullable|boolean',
            'status' => 'required|in:active,inactive,dismissed',
            'hire_date' => 'nullable|date',
            'dismissal_date' => 'nullable|date|after_or_equal:hire_date',
            'requires_qr' => 'nullable|boolean',
            'registered_device_uuid' => 'nullable|string|max:150',
            'note' => 'nullable|string|max:1000',
            'worker_type_ids' => 'nullable|array',
            'worker_type_ids.*' => 'exists:worker_types,id',
            'primary_worker_type_id' => 'nullable|exists:worker_types,id',
            'primary_branch_id' => 'nullable|exists:attendance_branches,id',
            'managed_branch_ids' => 'nullable|array',
            'managed_branch_ids.*' => 'exists:attendance_branches,id',
            'game_ids' => 'nullable|array',
            'game_ids.*' => 'integer',
        ]);

        $validated['is_intern'] = $request->boolean('is_intern');
        $validated['requires_qr'] = $request->boolean('requires_qr', true);

        try {
            $employee = $this->employeeService->create($validated, auth()->id());
            return redirect()->route('employees.index')
                ->with('success', "«{$employee->full_name}» adlı işçi uğurla yaradıldı.");
        } catch (Exception $e) {
            return back()->withInput()->withErrors(['general' => $e->getMessage()]);
        }
    }

    /**
     * İşçinin redaktə forması.
     */
    public function edit(Employee $employee): View
    {
        $employee->load(['workerTypes', 'branches', 'games', 'statusHistories']);

        $branches = AttendanceBranch::where('is_active', true)->orderBy('name')->get();
        $workerTypes = WorkerType::where('is_active', true)->orderBy('tabel_sort_order')->get();
        $games = app(PortalWebsiteService::class)->getAllGames();

        return view('employees.edit', compact('employee', 'branches', 'workerTypes', 'games'));
    }

    /**
     * İşçi məlumatlarını yeniləyir.
     */
    public function update(Request $request, Employee $employee): RedirectResponse
    {
        $validated = $request->validate([
            'full_name' => 'required|string|max:255',
            'first_name' => 'nullable|string|max:100',
            'last_name' => 'nullable|string|max:100',
            'tabel_display_name' => 'nullable|string|max:100',
            'phone' => 'nullable|string|max:30',
            'pin_code' => 'nullable|string|max:10',
            'gender' => 'required|in:male,female',
            'is_intern' => 'nullable|boolean',
            'status' => 'required|in:active,inactive,dismissed',
            'hire_date' => 'nullable|date',
            'dismissal_date' => 'nullable|date|after_or_equal:hire_date',
            'requires_qr' => 'nullable|boolean',
            'registered_device_uuid' => 'nullable|string|max:150',
            'note' => 'nullable|string|max:1000',
            'worker_type_ids' => 'nullable|array',
            'worker_type_ids.*' => 'exists:worker_types,id',
            'primary_worker_type_id' => 'nullable|exists:worker_types,id',
            'primary_branch_id' => 'nullable|exists:attendance_branches,id',
            'managed_branch_ids' => 'nullable|array',
            'managed_branch_ids.*' => 'exists:attendance_branches,id',
            'game_ids' => 'nullable|array',
            'game_ids.*' => 'integer',
            'promotion_effective_date' => 'nullable|date',
            'status_history_note' => 'nullable|string|max:255',
        ]);

        $validated['is_intern'] = $request->boolean('is_intern');
        $validated['requires_qr'] = $request->boolean('requires_qr', true);

        try {
            $this->employeeService->update($employee, $validated, auth()->id());
            return redirect()->route('employees.index')
                ->with('success', "«{$employee->full_name}» məlumatları uğurla yeniləndi.");
        } catch (Exception $e) {
            return back()->withInput()->withErrors(['general' => $e->getMessage()]);
        }
    }

    /**
     * İşçini silir (Soft delete).
     */
    public function destroy(Employee $employee): RedirectResponse
    {
        $name = $employee->full_name;
        $this->employeeService->delete($employee);

        return redirect()->route('employees.index')
            ->with('success', "«{$name}» adlı işçi arxivə göndərildi.");
    }

    /**
     * Saytdan və ya mövcud bazadan işçiləri idxal edir.
     */
    public function import(): RedirectResponse
    {
        try {
            $result = $this->employeeService->importFromPortalWebsite();
            $msg = "İşçilər uğurla idxal olundu! Yeni yaradıldı: {$result['created']}, Yeniləndi: {$result['updated']} (Cəmi: {$result['total_employees']}).";
            return redirect()->route('employees.index')->with('success', $msg);
        } catch (Exception $e) {
            return redirect()->route('employees.index')->withErrors(['general' => 'İdxal zamanı xəta: ' . $e->getMessage()]);
        }
    }

    /**
     * Aktiv / Deaktiv statusunu AJAX ilə dəyişir.
     */
    public function toggleStatus(Employee $employee): JsonResponse
    {
        $newStatus = ($employee->status === 'active') ? 'inactive' : 'active';
        $employee->update(['status' => $newStatus]);
        $this->employeeService->syncToAttendanceWorker($employee);

        return response()->json([
            'success' => true,
            'status' => $employee->status,
            'is_active' => ($employee->status === 'active'),
            'label' => $employee->status_label,
        ]);
    }

    /**
     * Stajor statusunu AJAX ilə dəyişir (və tarixçəyə yazır).
     */
    public function toggleIntern(Employee $employee): JsonResponse
    {
        $oldIsIntern = $employee->is_intern;
        $newIsIntern = !$oldIsIntern;

        // Əgər stajor edilirsə, vəzifələrin icazəsi yoxlanılır
        if ($newIsIntern) {
            $hasInvalid = $employee->workerTypes()->where('can_have_intern', false)->exists();
            if ($hasInvalid) {
                return response()->json([
                    'success' => false,
                    'message' => 'Bu işçinin vəzifələri arasında stajorluğa icazə verilməyən idarəedici vəzifə var.',
                ], 422);
            }
        }

        $employee->update(['is_intern' => $newIsIntern]);

        if ($oldIsIntern && !$newIsIntern) {
            $employee->statusHistories()->create([
                'event_type' => 'intern_promoted',
                'old_value' => ['is_intern' => true],
                'new_value' => ['is_intern' => false],
                'effective_date' => now()->toDateString(),
                'note' => 'Sürətli keçid: Stajorluqdan daimiyə keçirildi',
                'created_by_user_id' => auth()->id(),
            ]);
        }

        return response()->json([
            'success' => true,
            'is_intern' => $employee->is_intern,
            'label' => $employee->is_intern ? 'Stajor' : 'Daimi',
            'tabel_role_label' => $employee->tabel_role_label,
        ]);
    }
}
