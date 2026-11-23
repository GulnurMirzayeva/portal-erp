<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\WorkerType;
use App\Models\WorkerTypeBonusRule;
use App\Services\WorkerTypeService;
use Database\Seeders\PermissionSeeder;
use Database\Seeders\RoleSeeder;
use Database\Seeders\WorkerTypeSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class WorkerTypeModuleTest extends TestCase
{
    use RefreshDatabase;

    private function createUserWithRole(string $roleName): User
    {
        $this->seed(PermissionSeeder::class);
        $this->seed(RoleSeeder::class);

        $user = User::factory()->create();
        $user->assignRole($roleName);

        return $user;
    }

    public function test_seed_defaults_creates_expected_worker_types_and_bonus_rules(): void
    {
        $this->seed(WorkerTypeSeeder::class);

        // Idempotent təkrar çağırış dublikat yaratmamalıdır
        $second = app(WorkerTypeService::class)->seedDefaults();
        $this->assertSame(0, $second['created_types']);
        $this->assertSame(0, $second['created_rules']);

        $kurator = WorkerType::where('slug', 'kurator')->firstOrFail();
        $this->assertSame('K', $kurator->short_code);
        $this->assertSame(WorkerType::CATEGORY_MANAGEMENT, $kurator->category);
        $this->assertFalse($kurator->is_bonus_eligible);
        $this->assertFalse($kurator->can_have_intern);
        $this->assertTrue($kurator->show_in_tabel);

        $aktyor = WorkerType::where('slug', 'aktyor')->firstOrFail();
        $this->assertSame('Aktrisa', $aktyor->female_name);
        $this->assertSame('akt', $aktyor->short_code);
        $this->assertSame('aktrisa', $aktyor->female_short_code);
        $this->assertTrue($aktyor->is_bonus_eligible);
        $this->assertTrue($aktyor->can_have_intern);
        $this->assertTrue($aktyor->requires_game);
        $this->assertSame('actors', $aktyor->sale_slot_key);
        $this->assertSame(1.0, (float) $aktyor->defaultBonusRule->points);

        $muhasib = WorkerType::where('slug', 'muhasib')->firstOrFail();
        $this->assertSame(WorkerType::SCOPE_OFFICE, $muhasib->scope);
        $this->assertFalse($muhasib->show_in_tabel);
        $this->assertFalse($muhasib->is_bonus_eligible);
    }

    public function test_management_or_non_bonus_type_cannot_allow_interns(): void
    {
        $admin = $this->createUserWithRole('Admin');

        $response = $this->actingAs($admin)->post(route('worker-types.store'), [
            'name' => 'Baş Kurator',
            'short_code' => 'BK',
            'scope' => WorkerType::SCOPE_BRANCH,
            'category' => WorkerType::CATEGORY_MANAGEMENT,
            'tabel_sort_order' => 5,
            'default_pay_model' => 'fixed_monthly',
            'is_bonus_eligible' => '0',
            'can_have_intern' => '1',
            'show_in_tabel' => '1',
            'is_active' => '1',
        ]);

        $response->assertSessionHasErrors('can_have_intern');
        $this->assertDatabaseMissing('worker_types', ['name' => 'Baş Kurator']);
    }

    public function test_admin_can_create_edit_toggle_and_delete_worker_type_with_game_rules(): void
    {
        $admin = $this->createUserWithRole('Admin');

        $createResp = $this->actingAs($admin)->post(route('worker-types.store'), [
            'name' => 'Animasiya Aktyoru',
            'female_name' => 'Animasiya Aktrisası',
            'short_code' => 'anim',
            'female_short_code' => 'anim-q',
            'scope' => WorkerType::SCOPE_BRANCH,
            'category' => WorkerType::CATEGORY_OPERATIONAL,
            'sale_slot_key' => 'actors',
            'tabel_sort_order' => 58,
            'default_pay_model' => 'hybrid',
            'badge_color' => '#dc2626',
            'is_bonus_eligible' => '1',
            'can_have_intern' => '1',
            'requires_game' => '1',
            'show_in_tabel' => '1',
            'can_substitute' => '1',
            'is_active' => '1',
            'default_points' => '1.00',
            'game_rules' => [
                [
                    'portal_game_id' => 15,
                    'game_name' => 'Drakula',
                    'points' => '1.50',
                    'is_active' => '1',
                ],
            ],
        ]);

        $createResp->assertRedirect(route('worker-types.index'));

        $type = WorkerType::where('name', 'Animasiya Aktyoru')->firstOrFail();
        $this->assertSame(1.0, (float) $type->defaultBonusRule->points);
        $this->assertSame(1.5, (float) $type->gameBonusRules()->where('portal_game_id', 15)->first()->points);

        // Index, Create, Edit səhifələrinin Blade renderini yoxlayırıq
        $this->actingAs($admin)->get(route('worker-types.index'))
            ->assertOk()
            ->assertSee('Animasiya Aktyoru')
            ->assertSee('anim');
        $this->actingAs($admin)->get(route('worker-types.create'))->assertOk();
        $this->actingAs($admin)->get(route('worker-types.edit', $type->id))
            ->assertOk()
            ->assertSee('Drakula');

        // Edit: oyun xüsusi xalını yeniləyirik və default xalı 1.20 edirik
        $updateResp = $this->actingAs($admin)->put(route('worker-types.update', $type->id), [
            'name' => 'Animasiya Aktyoru',
            'female_name' => 'Animasiya Aktrisası',
            'short_code' => 'anim',
            'female_short_code' => 'anim-q',
            'scope' => WorkerType::SCOPE_BRANCH,
            'category' => WorkerType::CATEGORY_OPERATIONAL,
            'sale_slot_key' => 'actors',
            'tabel_sort_order' => 58,
            'default_pay_model' => 'hybrid',
            'badge_color' => '#dc2626',
            'is_bonus_eligible' => '1',
            'can_have_intern' => '1',
            'requires_game' => '1',
            'show_in_tabel' => '1',
            'can_substitute' => '1',
            'is_active' => '1',
            'default_points' => '1.20',
            'game_rules' => [
                [
                    'portal_game_id' => 15,
                    'game_name' => 'Drakula',
                    'points' => '1.80',
                    'is_active' => '1',
                ],
            ],
        ]);

        $updateResp->assertRedirect(route('worker-types.index'));
        $type->refresh();
        $this->assertSame(1.2, (float) $type->defaultBonusRule->points);
        $this->assertSame(1.8, (float) $type->gameBonusRules()->where('portal_game_id', 15)->first()->points);

        // Toggle active/inactive
        $toggleResp = $this->actingAs($admin)->patchJson(route('worker-types.toggle', $type->id));
        $toggleResp->assertOk()->assertJson(['success' => true, 'is_active' => false]);

        // Delete
        $delResp = $this->actingAs($admin)->delete(route('worker-types.destroy', $type->id));
        $delResp->assertRedirect(route('worker-types.index'));
        $this->assertDatabaseMissing('worker_types', ['id' => $type->id]);
    }

    public function test_visual_bonus_resolution_respects_game_override_max_role_and_intern_zero(): void
    {
        $this->seed(WorkerTypeSeeder::class);

        $aktyor = WorkerType::where('slug', 'aktyor')->firstOrFail();
        $operator = WorkerType::where('slug', 'operator')->firstOrFail();
        $resepsn = WorkerType::where('slug', 'resepsn')->firstOrFail();
        $adminType = WorkerType::where('slug', 'administrator')->firstOrFail();

        // Drakula (id=15) üçün Aktyora xüsusi 1.50 xal əlavə edirik
        WorkerTypeBonusRule::create([
            'worker_type_id' => $aktyor->id,
            'portal_game_id' => 15,
            'game_name' => 'Drakula',
            'points' => 1.50,
            'is_active' => true,
        ]);

        $service = app(WorkerTypeService::class);

        // 1. Default oyun (game_id=99) -> Aktyor (1.00) + Operator (0.50) => MAX = 1.00
        $resDefault = $service->resolveVisualPoints([$aktyor->id, $operator->id], 99, false);
        $this->assertSame(1.0, $resDefault['points']);
        $this->assertSame($aktyor->id, $resDefault['winning_worker_type_id']);

        // 2. Drakula (game_id=15) -> Aktyor (1.50) + Operator (0.50) => MAX = 1.50
        $resDracula = $service->resolveVisualPoints([$aktyor->id, $operator->id], 15, false);
        $this->assertSame(1.5, $resDracula['points']);

        // 3. İdarəedici + Bonus gətirən (Administrator + Resepşn) => yalnız Resepşn (0.30)
        $resMixed = $service->resolveVisualPoints([$adminType->id, $resepsn->id], 15, false);
        $this->assertSame(0.3, $resMixed['points']);
        $this->assertSame($resepsn->id, $resMixed['winning_worker_type_id']);

        // 4. Stajor (is_intern = true) => HƏMİŞƏ 0.00
        $resIntern = $service->resolveVisualPoints([$aktyor->id, $resepsn->id], 15, true);
        $this->assertSame(0.0, $resIntern['points']);
        $this->assertTrue($resIntern['is_intern']);
    }

    public function test_tabel_label_composition_handles_gender_multi_role_intern_and_substitute(): void
    {
        $this->seed(WorkerTypeSeeder::class);
        $service = app(WorkerTypeService::class);

        $kurator = WorkerType::where('slug', 'kurator')->firstOrFail();
        $adminType = WorkerType::where('slug', 'administrator')->firstOrFail();
        $sup = WorkerType::where('slug', 'supervayzer')->firstOrFail();
        $res = WorkerType::where('slug', 'resepsn')->firstOrFail();
        $koor = WorkerType::where('slug', 'koordinator')->firstOrFail();
        $akt = WorkerType::where('slug', 'aktyor')->firstOrFail();

        $this->assertSame('K', $service->composeTabelLabel(collect([$kurator])));
        $this->assertSame('adm-res', $service->composeTabelLabel(collect([$res, $adminType])));
        $this->assertSame('sup-res', $service->composeTabelLabel(collect([$res, $sup])));
        $this->assertSame('koor-akt', $service->composeTabelLabel(collect([$akt, $koor]), 'male'));
        $this->assertSame('koor-aktrisa', $service->composeTabelLabel(collect([$akt, $koor]), 'female'));
        $this->assertSame('staj-akt', $service->composeTabelLabel(collect([$akt]), 'male', true));
        $this->assertSame('staj-res', $service->composeTabelLabel(collect([$res]), 'female', true));
        $this->assertSame('əvəz aktyor', $service->composeTabelLabel(collect([$akt]), 'male', false, true));
        $this->assertSame('əvəz aktrisa', $service->composeTabelLabel(collect([$akt]), 'female', false, true));
    }

    public function test_director_can_view_worker_types_but_cannot_mutate(): void
    {
        $director = $this->createUserWithRole('Director');

        $this->actingAs($director)->get(route('worker-types.index'))->assertOk();
        $this->actingAs($director)->get(route('worker-types.create'))->assertForbidden();
        $this->actingAs($director)->post(route('worker-types.store'), [])->assertForbidden();
    }

    public function test_api_endpoints_require_api_key_and_return_types_and_bonus(): void
    {
        $this->seed(WorkerTypeSeeder::class);
        $token = config('services.portal_website.token');

        // Açarsız sorğu bloklanmalıdır
        $this->getJson('/api/erp/worker-types')->assertStatus(401);

        // Düzgün açarla tiplər gəlir
        $listResp = $this->withHeaders(['X-ERP-API-KEY' => $token])
            ->getJson('/api/erp/worker-types?bonus_only=1');

        $listResp->assertOk()
            ->assertJson(['success' => true]);

        $types = collect($listResp->json('worker_types'));
        $this->assertTrue($types->every(fn ($t) => $t['is_bonus_eligible'] === true));

        $aktyor = WorkerType::where('slug', 'aktyor')->firstOrFail();
        $resepsn = WorkerType::where('slug', 'resepsn')->firstOrFail();

        $calcResp = $this->withHeaders(['X-ERP-API-KEY' => $token])
            ->postJson('/api/erp/worker-types/calculate-visual-bonus', [
                'worker_type_ids' => [$aktyor->id, $resepsn->id],
                'game_id' => 10,
                'is_intern' => false,
            ]);

        $calcResp->assertOk()
            ->assertJsonPath('result.points', 1);

        $internCalcResp = $this->withHeaders(['X-ERP-API-KEY' => $token])
            ->postJson('/api/erp/worker-types/calculate-visual-bonus', [
                'worker_type_ids' => [$aktyor->id],
                'game_id' => 10,
                'is_intern' => true,
            ]);

        $internCalcResp->assertOk()
            ->assertJsonPath('result.points', 0);
    }
}
