<?php

namespace Tests\Feature;

use App\Models\AttendanceBranch;
use App\Models\AttendanceWorker;
use App\Models\Employee;
use App\Models\EmployeeStatusHistory;
use App\Models\User;
use App\Models\WorkerType;
use App\Services\EmployeeService;
use Database\Seeders\PermissionSeeder;
use Database\Seeders\RoleSeeder;
use Database\Seeders\WorkerTypeSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class EmployeeModuleTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(PermissionSeeder::class);
        $this->seed(RoleSeeder::class);
        $this->seed(WorkerTypeSeeder::class);
    }

    private function createUserWithRole(string $roleName): User
    {
        $user = User::factory()->create();
        $user->assignRole($roleName);
        return $user;
    }

    private function createBranch(string $name, int $portalBranchId = 1): AttendanceBranch
    {
        return AttendanceBranch::create([
            'portal_branch_id' => $portalBranchId,
            'name' => $name,
            'slug' => \Illuminate\Support\Str::slug($name),
            'qr_token' => \Illuminate\Support\Str::random(24),
            'is_active' => true,
        ]);
    }

    public function test_admin_can_create_employee_with_roles_branches_and_games(): void
    {
        $admin = $this->createUserWithRole('Admin');
        $branch1 = $this->createBranch('Gənclik', 10);
        $branch2 = $this->createBranch('28 May', 20);

        $aktyor = WorkerType::where('slug', 'aktyor')->firstOrFail();
        $resepsn = WorkerType::where('slug', 'resepsn')->firstOrFail();

        $response = $this->actingAs($admin)->post(route('employees.store'), [
            'full_name' => 'Fərid Əliyev',
            'phone' => '+994 50 111 22 33',
            'pin_code' => '5566',
            'gender' => 'male',
            'is_intern' => '0',
            'status' => 'active',
            'hire_date' => '2026-05-01',
            'requires_qr' => '1',
            'primary_branch_id' => $branch1->id,
            'managed_branch_ids' => [$branch2->id],
            'worker_type_ids' => [$aktyor->id, $resepsn->id],
            'primary_worker_type_id' => $aktyor->id,
            'game_ids' => [15], // Drakula
        ]);

        $response->assertRedirect(route('employees.index'));

        $emp = Employee::where('full_name', 'Fərid Əliyev')->firstOrFail();
        $this->assertSame('Fərid', $emp->first_name);
        $this->assertSame('Əliyev', $emp->last_name);
        $this->assertSame('Əliyev', $emp->tabel_name);
        $this->assertSame('res-akt', $emp->tabel_role_label);
        $this->assertSame('Kişi', $emp->gender_label);
        $this->assertFalse($emp->is_intern);

        // Filiallar
        $this->assertSame($branch1->id, $emp->primaryBranch()?->id);
        $this->assertTrue($emp->managedBranches->contains($branch2->id));

        // Oyunlar
        $this->assertCount(1, $emp->games);
        $this->assertSame(15, (int) $emp->games->first()->portal_game_id);

        // QR AttendanceWorker sinxronizasiyası
        $attWorker = AttendanceWorker::where('phone', '+994 50 111 22 33')->firstOrFail();
        $this->assertSame('Fərid Əliyev', $attWorker->name);
        $this->assertSame('5566', $attWorker->pin_code);
        $this->assertSame($branch1->id, $attWorker->attendance_branch_id);
    }

    public function test_intern_cannot_be_assigned_management_roles(): void
    {
        $admin = $this->createUserWithRole('Admin');
        $kurator = WorkerType::where('slug', 'kurator')->firstOrFail();

        $response = $this->actingAs($admin)->post(route('employees.store'), [
            'full_name' => 'Elvin Məmmədov',
            'gender' => 'male',
            'is_intern' => '1',
            'status' => 'active',
            'worker_type_ids' => [$kurator->id],
        ]);

        $response->assertSessionHasErrors('general');
        $this->assertDatabaseMissing('employees', ['full_name' => 'Elvin Məmmədov']);
    }

    public function test_promoting_intern_to_permanent_creates_status_history(): void
    {
        $admin = $this->createUserWithRole('Admin');
        $branch = $this->createBranch('Elmlər', 30);
        $aktyor = WorkerType::where('slug', 'aktyor')->firstOrFail();

        $service = app(EmployeeService::class);
        $emp = $service->create([
            'full_name' => 'Aysel Kərimova',
            'gender' => 'female',
            'is_intern' => true,
            'status' => 'active',
            'primary_branch_id' => $branch->id,
            'worker_type_ids' => [$aktyor->id],
        ], $admin->id);

        $this->assertTrue($emp->is_intern);
        $this->assertSame('staj-aktrisa', $emp->tabel_role_label);

        // İndi stajordan daimiyə keçiririk
        $updateResp = $this->actingAs($admin)->put(route('employees.update', $emp->id), [
            'full_name' => 'Aysel Kərimova',
            'gender' => 'female',
            'is_intern' => '0',
            'status' => 'active',
            'worker_type_ids' => [$aktyor->id],
            'promotion_effective_date' => '2026-11-15',
            'status_history_note' => 'İmtahanı uğurla verdi',
        ]);

        $updateResp->assertRedirect(route('employees.index'));
        $emp->refresh();
        $this->assertFalse($emp->is_intern);
        $this->assertSame('aktrisa', $emp->tabel_role_label);

        // Tarixçə qeydi yoxlanılır
        $history = EmployeeStatusHistory::where('employee_id', $emp->id)
            ->where('event_type', 'intern_promoted')
            ->firstOrFail();

        $this->assertSame('2026-11-15', $history->effective_date->format('Y-m-d'));
        $this->assertStringContainsString('İmtahanı', $history->note);
    }

    public function test_curator_can_manage_multiple_branches(): void
    {
        $admin = $this->createUserWithRole('Admin');
        $b1 = $this->createBranch('Filial 1', 1);
        $b2 = $this->createBranch('Filial 2', 2);
        $b3 = $this->createBranch('Filial 3', 3);
        $kurator = WorkerType::where('slug', 'kurator')->firstOrFail();

        $service = app(EmployeeService::class);
        $emp = $service->create([
            'full_name' => 'Baş Kurator Rəşad',
            'gender' => 'male',
            'is_intern' => false,
            'status' => 'active',
            'primary_branch_id' => $b1->id,
            'managed_branch_ids' => [$b2->id, $b3->id],
            'worker_type_ids' => [$kurator->id],
        ]);

        $this->assertCount(3, $emp->branches);
        $this->assertSame($b1->id, $emp->primaryBranch()?->id);
        $this->assertCount(2, $emp->managedBranches);
        $this->assertSame('K', $emp->tabel_role_label);
    }

    public function test_ajax_toggle_status_and_intern(): void
    {
        $admin = $this->createUserWithRole('Admin');
        $aktyor = WorkerType::where('slug', 'aktyor')->firstOrFail();

        $emp = Employee::create([
            'full_name' => 'Murad Həsənov',
            'phone' => '+994 55 999 88 77',
            'pin_code' => '9988',
            'gender' => 'male',
            'is_intern' => false,
            'status' => 'active',
        ]);
        $emp->workerTypes()->sync([$aktyor->id => ['is_primary' => true]]);

        // 1. Toggle status
        $statusResp = $this->actingAs($admin)->patchJson(route('employees.toggle', $emp->id));
        $statusResp->assertOk()->assertJson(['success' => true, 'status' => 'inactive']);
        $emp->refresh();
        $this->assertSame('inactive', $emp->status);

        // 2. Toggle intern
        $internResp = $this->actingAs($admin)->patchJson(route('employees.toggle-intern', $emp->id));
        $internResp->assertOk()->assertJson(['success' => true, 'is_intern' => true]);
        $emp->refresh();
        $this->assertTrue($emp->is_intern);
    }

    public function test_views_render_successfully(): void
    {
        $admin = $this->createUserWithRole('Admin');
        $branch = $this->createBranch('Nərimanov', 50);
        $aktyor = WorkerType::where('slug', 'aktyor')->firstOrFail();

        $emp = Employee::create([
            'full_name' => 'Kənan Vəliyev',
            'phone' => '+994 70 333 44 55',
            'pin_code' => '3344',
            'gender' => 'male',
            'is_intern' => false,
            'status' => 'active',
        ]);
        $emp->workerTypes()->sync([$aktyor->id => ['is_primary' => true]]);
        $emp->branches()->sync([$branch->id => ['is_primary' => true]]);

        // Index
        $this->actingAs($admin)->get(route('employees.index'))
            ->assertOk()
            ->assertSee('Kənan Vəliyev')
            ->assertSee('Nərimanov');

        // Create
        $this->actingAs($admin)->get(route('employees.create'))
            ->assertOk()
            ->assertSee('Yeni Vahid İşçi Qeydiyyatı');

        // Edit
        $this->actingAs($admin)->get(route('employees.edit', $emp->id))
            ->assertOk()
            ->assertSee('Kənan Vəliyev');
    }

    public function test_director_can_view_but_cannot_mutate_employees(): void
    {
        $director = $this->createUserWithRole('Director');

        $this->actingAs($director)->get(route('employees.index'))->assertOk();
        $this->actingAs($director)->get(route('employees.create'))->assertForbidden();
        $this->actingAs($director)->post(route('employees.store'), [])->assertForbidden();
    }

    public function test_import_creates_employees_from_existing_workers(): void
    {
        $admin = $this->createUserWithRole('Admin');
        $branch = $this->createBranch('Sahil', 99);

        // AttendanceWorker mövcuddur
        AttendanceWorker::create([
            'portal_worker_id' => 888,
            'portal_branch_id' => 99,
            'attendance_branch_id' => $branch->id,
            'name' => 'Tərlan Quliyev',
            'phone' => '+994 50 888 77 66',
            'pin_code' => '7766',
            'is_active' => true,
        ]);

        $resp = $this->actingAs($admin)->post(route('employees.import'));
        $resp->assertRedirect(route('employees.index'));

        $this->assertDatabaseHas('employees', [
            'portal_worker_id' => 888,
            'phone' => '+994 50 888 77 66',
            'full_name' => 'Tərlan Quliyev',
        ]);

        $emp = Employee::where('phone', '+994 50 888 77 66')->firstOrFail();
        $this->assertSame($branch->id, $emp->primaryBranch()?->id);
    }
}
