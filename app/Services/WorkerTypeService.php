<?php

namespace App\Services;

use App\Models\WorkerType;
use App\Models\WorkerTypeBonusRule;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class WorkerTypeService
{
    /**
     * Sistem üçün standart işçi tipləri və ilkin vizual bonus xal qaydaları.
     */
    public function getDefaultDefinitions(): array
    {
        return [
            // 1. Filial — İdarəedici (satışda seçilməyən, stajoru olmayan)
            [
                'portal_worker_type_id' => 10,
                'name' => 'Kurator',
                'slug' => 'kurator',
                'short_code' => 'K',
                'scope' => WorkerType::SCOPE_BRANCH,
                'category' => WorkerType::CATEGORY_MANAGEMENT,
                'is_bonus_eligible' => false,
                'can_have_intern' => false,
                'requires_game' => false,
                'show_in_tabel' => true,
                'can_substitute' => false,
                'sale_slot_key' => null,
                'tabel_sort_order' => 10,
                'default_pay_model' => 'fixed_monthly',
                'badge_color' => '#7c3aed',
                'description' => 'Filialın ümumi idarəçisi. Tabeldə 1-ci sırada gəlir, satışda seçilmir.',
                'default_points' => null,
            ],
            [
                'portal_worker_type_id' => 3,
                'name' => 'Administrator',
                'slug' => 'administrator',
                'short_code' => 'adm',
                'scope' => WorkerType::SCOPE_BRANCH,
                'category' => WorkerType::CATEGORY_MANAGEMENT,
                'is_bonus_eligible' => false,
                'can_have_intern' => false,
                'requires_game' => false,
                'show_in_tabel' => true,
                'can_substitute' => true,
                'sale_slot_key' => null,
                'tabel_sort_order' => 20,
                'default_pay_model' => 'hybrid',
                'badge_color' => '#4f46e5',
                'description' => 'Filial inzibatçısı. Çox vaxt Resepşn və ya Aktyorla birgə (adm-res, adm-akt) təyin olunur.',
                'default_points' => null,
            ],
            [
                'portal_worker_type_id' => 11,
                'name' => 'Supervayzer',
                'slug' => 'supervayzer',
                'short_code' => 'sup',
                'scope' => WorkerType::SCOPE_BRANCH,
                'category' => WorkerType::CATEGORY_MANAGEMENT,
                'is_bonus_eligible' => false,
                'can_have_intern' => false,
                'requires_game' => false,
                'show_in_tabel' => true,
                'can_substitute' => true,
                'sale_slot_key' => null,
                'tabel_sort_order' => 30,
                'default_pay_model' => 'hybrid',
                'badge_color' => '#0284c7',
                'description' => 'Smen və oyun nəzarətçisi (məs: sup-res, sup-akt). Satışda ayrıca rol kimi seçilmir.',
                'default_points' => null,
            ],
            [
                'portal_worker_type_id' => 22,
                'name' => 'Koordinator',
                'slug' => 'koordinator',
                'short_code' => 'koor',
                'scope' => WorkerType::SCOPE_BRANCH,
                'category' => WorkerType::CATEGORY_MANAGEMENT,
                'is_bonus_eligible' => false,
                'can_have_intern' => false,
                'requires_game' => false,
                'show_in_tabel' => true,
                'can_substitute' => true,
                'sale_slot_key' => null,
                'tabel_sort_order' => 50,
                'default_pay_model' => 'hybrid',
                'badge_color' => '#0d9488',
                'description' => 'Oyun koordinasiyası rolu (məs: koor-akt). Satışda ayrıca bonus roluna daxil deyil.',
                'default_points' => null,
            ],

            // 2. Filial — Bonus Gətirən (satışda seçilənlər və stajor qəbul edənlər)
            [
                'portal_worker_type_id' => 1,
                'name' => 'Resepşn',
                'slug' => 'resepsn',
                'short_code' => 'res',
                'scope' => WorkerType::SCOPE_BRANCH,
                'category' => WorkerType::CATEGORY_OPERATIONAL,
                'is_bonus_eligible' => true,
                'can_have_intern' => true,
                'requires_game' => false,
                'show_in_tabel' => true,
                'can_substitute' => true,
                'sale_slot_key' => 'receptionists',
                'tabel_sort_order' => 40,
                'default_pay_model' => 'per_shift',
                'badge_color' => '#107c41',
                'description' => 'Qonaq qarşılama və satış qeydiyyatı. Stajor (staj-res) ola bilər.',
                'default_points' => 0.30,
            ],
            [
                'portal_worker_type_id' => 7,
                'name' => 'Hostes',
                'slug' => 'hostes',
                'short_code' => 'hos',
                'scope' => WorkerType::SCOPE_BRANCH,
                'category' => WorkerType::CATEGORY_OPERATIONAL,
                'is_bonus_eligible' => true,
                'can_have_intern' => true,
                'requires_game' => false,
                'show_in_tabel' => true,
                'can_substitute' => true,
                'sale_slot_key' => 'hostesses',
                'tabel_sort_order' => 42,
                'default_pay_model' => 'per_shift',
                'badge_color' => '#059669',
                'description' => 'Qonaq yönləndirmə və brifinq. Stajor (staj-hos) ola bilər.',
                'default_points' => 0.30,
            ],
            [
                'portal_worker_type_id' => 2,
                'name' => 'Operator',
                'slug' => 'operator',
                'short_code' => 'op',
                'scope' => WorkerType::SCOPE_BRANCH,
                'category' => WorkerType::CATEGORY_OPERATIONAL,
                'is_bonus_eligible' => true,
                'can_have_intern' => true,
                'requires_game' => false,
                'show_in_tabel' => true,
                'can_substitute' => true,
                'sale_slot_key' => 'operators',
                'tabel_sort_order' => 45,
                'default_pay_model' => 'per_shift',
                'badge_color' => '#2563eb',
                'description' => 'Kamera və oyun idarəetmə operatoru. Stajor (staj-op) ola bilər.',
                'default_points' => 0.50,
            ],
            [
                'portal_worker_type_id' => 8,
                'name' => 'Aktyor',
                'female_name' => 'Aktrisa',
                'slug' => 'aktyor',
                'short_code' => 'akt',
                'female_short_code' => 'aktrisa',
                'scope' => WorkerType::SCOPE_BRANCH,
                'category' => WorkerType::CATEGORY_OPERATIONAL,
                'is_bonus_eligible' => true,
                'can_have_intern' => true,
                'requires_game' => true,
                'show_in_tabel' => true,
                'can_substitute' => true,
                'sale_slot_key' => 'actors',
                'tabel_sort_order' => 55,
                'default_pay_model' => 'hybrid',
                'badge_color' => '#dc2626',
                'description' => 'Oyun aktyoru/aktrisası. Cinsə görə Aktyor/Aktrisa görünür, oyun tələb edir, stajor (staj-akt) ola bilər.',
                'default_points' => 1.00,
            ],

            // 3. Filial — Dəstək
            [
                'portal_worker_type_id' => 12,
                'name' => 'Xadimə',
                'slug' => 'xadime',
                'short_code' => 'xadimə',
                'scope' => WorkerType::SCOPE_BRANCH,
                'category' => WorkerType::CATEGORY_SUPPORT,
                'is_bonus_eligible' => false,
                'can_have_intern' => false,
                'requires_game' => false,
                'show_in_tabel' => true,
                'can_substitute' => true,
                'sale_slot_key' => null,
                'tabel_sort_order' => 70,
                'default_pay_model' => 'per_shift',
                'badge_color' => '#64748b',
                'description' => 'Filial təmizlik əməkdaşı.',
                'default_points' => null,
            ],

            // 4. Ofis Vəzifələri (Filial tabelinə və satışa daxil deyil)
            [
                'portal_worker_type_id' => 5,
                'name' => 'Direktor Müavini',
                'slug' => 'direktor-muavini',
                'short_code' => 'dir-m',
                'scope' => WorkerType::SCOPE_OFFICE,
                'category' => WorkerType::CATEGORY_MANAGEMENT,
                'is_bonus_eligible' => false,
                'can_have_intern' => false,
                'requires_game' => false,
                'show_in_tabel' => false,
                'can_substitute' => false,
                'sale_slot_key' => null,
                'tabel_sort_order' => 100,
                'default_pay_model' => 'fixed_monthly',
                'badge_color' => '#334155',
                'default_points' => null,
            ],
            [
                'portal_worker_type_id' => 6,
                'name' => 'HR',
                'slug' => 'hr',
                'short_code' => 'hr',
                'scope' => WorkerType::SCOPE_OFFICE,
                'category' => WorkerType::CATEGORY_MANAGEMENT,
                'is_bonus_eligible' => false,
                'can_have_intern' => false,
                'requires_game' => false,
                'show_in_tabel' => false,
                'can_substitute' => false,
                'sale_slot_key' => null,
                'tabel_sort_order' => 105,
                'default_pay_model' => 'fixed_monthly',
                'badge_color' => '#334155',
                'default_points' => null,
            ],
            [
                'portal_worker_type_id' => 4,
                'name' => 'Mühasib',
                'slug' => 'muhasib',
                'short_code' => 'müh',
                'scope' => WorkerType::SCOPE_OFFICE,
                'category' => WorkerType::CATEGORY_SUPPORT,
                'is_bonus_eligible' => false,
                'can_have_intern' => false,
                'requires_game' => false,
                'show_in_tabel' => false,
                'can_substitute' => false,
                'sale_slot_key' => null,
                'tabel_sort_order' => 110,
                'default_pay_model' => 'fixed_monthly',
                'badge_color' => '#475569',
                'default_points' => null,
            ],
            [
                'portal_worker_type_id' => 18,
                'name' => 'Mühasib köməkçisi',
                'slug' => 'muhasib-komekcisi',
                'short_code' => 'müh-k',
                'scope' => WorkerType::SCOPE_OFFICE,
                'category' => WorkerType::CATEGORY_SUPPORT,
                'is_bonus_eligible' => false,
                'can_have_intern' => false,
                'requires_game' => false,
                'show_in_tabel' => false,
                'can_substitute' => false,
                'sale_slot_key' => null,
                'tabel_sort_order' => 115,
                'default_pay_model' => 'fixed_monthly',
                'badge_color' => '#475569',
                'default_points' => null,
            ],
            [
                'portal_worker_type_id' => 13,
                'name' => 'Auditor',
                'slug' => 'auditor',
                'short_code' => 'aud',
                'scope' => WorkerType::SCOPE_OFFICE,
                'category' => WorkerType::CATEGORY_SUPPORT,
                'is_bonus_eligible' => false,
                'can_have_intern' => false,
                'requires_game' => false,
                'show_in_tabel' => false,
                'can_substitute' => false,
                'sale_slot_key' => null,
                'tabel_sort_order' => 120,
                'default_pay_model' => 'fixed_monthly',
                'badge_color' => '#475569',
                'default_points' => null,
            ],
            [
                'portal_worker_type_id' => 14,
                'name' => 'Ofis meneceri',
                'slug' => 'ofis-meneceri',
                'short_code' => 'ofis-m',
                'scope' => WorkerType::SCOPE_OFFICE,
                'category' => WorkerType::CATEGORY_SUPPORT,
                'is_bonus_eligible' => false,
                'can_have_intern' => false,
                'requires_game' => false,
                'show_in_tabel' => false,
                'can_substitute' => false,
                'sale_slot_key' => null,
                'tabel_sort_order' => 125,
                'default_pay_model' => 'fixed_monthly',
                'badge_color' => '#475569',
                'default_points' => null,
            ],
            [
                'portal_worker_type_id' => 15,
                'name' => 'Ssenarist',
                'slug' => 'ssenarist',
                'short_code' => 'ssen',
                'scope' => WorkerType::SCOPE_OFFICE,
                'category' => WorkerType::CATEGORY_SUPPORT,
                'is_bonus_eligible' => false,
                'can_have_intern' => false,
                'requires_game' => false,
                'show_in_tabel' => false,
                'can_substitute' => false,
                'sale_slot_key' => null,
                'tabel_sort_order' => 130,
                'default_pay_model' => 'fixed_monthly',
                'badge_color' => '#475569',
                'default_points' => null,
            ],
            [
                'portal_worker_type_id' => 16,
                'name' => 'Texnik',
                'slug' => 'texnik',
                'short_code' => 'tex',
                'scope' => WorkerType::SCOPE_BOTH,
                'category' => WorkerType::CATEGORY_SUPPORT,
                'is_bonus_eligible' => false,
                'can_have_intern' => false,
                'requires_game' => false,
                'show_in_tabel' => false,
                'can_substitute' => false,
                'sale_slot_key' => null,
                'tabel_sort_order' => 135,
                'default_pay_model' => 'fixed_monthly',
                'badge_color' => '#475569',
                'default_points' => null,
            ],
            [
                'portal_worker_type_id' => 17,
                'name' => 'Dizayner',
                'slug' => 'dizayner',
                'short_code' => 'diz',
                'scope' => WorkerType::SCOPE_OFFICE,
                'category' => WorkerType::CATEGORY_SUPPORT,
                'is_bonus_eligible' => false,
                'can_have_intern' => false,
                'requires_game' => false,
                'show_in_tabel' => false,
                'can_substitute' => false,
                'sale_slot_key' => null,
                'tabel_sort_order' => 140,
                'default_pay_model' => 'fixed_monthly',
                'badge_color' => '#475569',
                'default_points' => null,
            ],
            [
                'portal_worker_type_id' => 19,
                'name' => 'Web mütəxəssisi',
                'slug' => 'web-mutexessisi',
                'short_code' => 'web',
                'scope' => WorkerType::SCOPE_OFFICE,
                'category' => WorkerType::CATEGORY_SUPPORT,
                'is_bonus_eligible' => false,
                'can_have_intern' => false,
                'requires_game' => false,
                'show_in_tabel' => false,
                'can_substitute' => false,
                'sale_slot_key' => null,
                'tabel_sort_order' => 145,
                'default_pay_model' => 'fixed_monthly',
                'badge_color' => '#475569',
                'default_points' => null,
            ],
            [
                'portal_worker_type_id' => 20,
                'name' => 'Marketing',
                'slug' => 'marketing',
                'short_code' => 'mkt',
                'scope' => WorkerType::SCOPE_OFFICE,
                'category' => WorkerType::CATEGORY_SUPPORT,
                'is_bonus_eligible' => false,
                'can_have_intern' => false,
                'requires_game' => false,
                'show_in_tabel' => false,
                'can_substitute' => false,
                'sale_slot_key' => null,
                'tabel_sort_order' => 150,
                'default_pay_model' => 'fixed_monthly',
                'badge_color' => '#475569',
                'default_points' => null,
            ],
        ];
    }

    /**
     * Standart işçi tiplərini və ilkin bonus qaydalarını idempotent şəkildə yaradır.
     */
    public function seedDefaults(): array
    {
        $this->ensurePermissions();

        $createdTypes = 0;
        $createdRules = 0;

        foreach ($this->getDefaultDefinitions() as $def) {
            $defaultPoints = $def['default_points'] ?? null;
            unset($def['default_points']);

            $type = WorkerType::where('slug', $def['slug'])->first();

            if (!$type && !empty($def['portal_worker_type_id'])) {
                $type = WorkerType::where('portal_worker_type_id', $def['portal_worker_type_id'])->first();
            }

            if (!$type) {
                $type = WorkerType::create($def);
                $createdTypes++;
            }

            if ($type->is_bonus_eligible && $defaultPoints !== null) {
                $hasDefaultRule = $type->bonusRules()->whereNull('portal_game_id')->exists();
                if (!$hasDefaultRule) {
                    WorkerTypeBonusRule::create([
                        'worker_type_id' => $type->id,
                        'portal_game_id' => null,
                        'game_name' => null,
                        'points' => $defaultPoints,
                        'is_active' => true,
                    ]);
                    $createdRules++;
                }
            }
        }

        return [
            'created_types' => $createdTypes,
            'created_rules' => $createdRules,
            'total_types' => WorkerType::count(),
        ];
    }

    /**
     * Dinamik işçi tipləri üçün Spatie icazələrini təmin edir.
     */
    public function ensurePermissions(): void
    {
        try {
            if (class_exists(\Spatie\Permission\Models\Permission::class) && class_exists(\Spatie\Permission\Models\Role::class)) {
                $perms = ['worker-types.view', 'worker-types.create', 'worker-types.edit', 'worker-types.delete'];
                foreach ($perms as $p) {
                    \Spatie\Permission\Models\Permission::firstOrCreate(['name' => $p, 'guard_name' => 'web']);
                }

                $admin = \Spatie\Permission\Models\Role::where('name', 'Admin')->where('guard_name', 'web')->first();
                $admin?->givePermissionTo($perms);

                $hr = \Spatie\Permission\Models\Role::where('name', 'HR')->where('guard_name', 'web')->first();
                $hr?->givePermissionTo($perms);

                $dir = \Spatie\Permission\Models\Role::where('name', 'Director')->where('guard_name', 'web')->first();
                $dir?->givePermissionTo(['worker-types.view']);
            }
        } catch (\Throwable $e) {
            Log::warning('WorkerTypeService ensurePermissions failed: ' . $e->getMessage());
        }
    }

    /**
     * Seçilmiş rollar, oyun və stajor statusuna görə vizual yarış xalını hesablayır.
     * Qayda: Stajor üçün 0; çoxlu rol seçildikdə yalnız ən yüksək xal götürülür.
     */
    public function resolveVisualPoints(array $workerTypeIds, ?int $portalGameId = null, bool $isIntern = false): array
    {
        if ($isIntern || empty($workerTypeIds)) {
            return [
                'points' => 0.0,
                'winning_worker_type_id' => null,
                'winning_worker_type_name' => null,
                'is_intern' => $isIntern,
                'breakdown' => [],
            ];
        }

        $types = WorkerType::with('bonusRules')
            ->whereIn('id', array_unique(array_map('intval', $workerTypeIds)))
            ->where('is_active', true)
            ->where('is_bonus_eligible', true)
            ->get();

        $maxPoints = 0.0;
        $winningType = null;
        $breakdown = [];

        foreach ($types as $type) {
            $pts = $type->resolvePointsForGame($portalGameId, false);
            $breakdown[] = [
                'worker_type_id' => $type->id,
                'name' => $type->name,
                'points' => $pts,
            ];

            if ($pts > $maxPoints) {
                $maxPoints = $pts;
                $winningType = $type;
            }
        }

        return [
            'points' => round($maxPoints, 2),
            'winning_worker_type_id' => $winningType?->id,
            'winning_worker_type_name' => $winningType?->name,
            'is_intern' => false,
            'breakdown' => $breakdown,
        ];
    }

    /**
     * Bir işçinin rollarını, cinsini, stajor və əvəzedici statusunu tabel etiketində birləşdirir.
     * Məs: "K", "adm-res", "sup-res", "koor-akt", "staj-akt", "əvəz-aktrisa".
     */
    public function composeTabelLabel(
        Collection $workerTypes,
        string $gender = 'male',
        bool $isIntern = false,
        bool $isSubstitute = false
    ): string {
        if ($workerTypes->isEmpty()) {
            return $isIntern ? 'staj' : ($isSubstitute ? 'əvəz' : '');
        }

        $sorted = $workerTypes->sortBy([
            ['tabel_sort_order', 'asc'],
            ['name', 'asc'],
        ]);

        $codes = $sorted->map(function (WorkerType $type) use ($gender, $isSubstitute) {
            if ($isSubstitute) {
                return mb_strtolower($type->getNameForGender($gender));
            }
            return $type->getShortCodeForGender($gender);
        })->filter()->unique()->values()->all();

        $combined = implode('-', $codes);

        if ($isIntern) {
            return 'staj-' . $combined;
        }

        if ($isSubstitute) {
            return 'əvəz ' . $combined;
        }

        return $combined;
    }

    /**
     * Tip üçün unikal slug generasiya edir.
     */
    public function generateUniqueSlug(string $name, ?int $ignoreId = null): string
    {
        $base = Str::slug($name);
        if (empty($base)) {
            $base = 'worker-type';
        }

        $slug = $base;
        $counter = 2;

        while (
            WorkerType::where('slug', $slug)
                ->when($ignoreId, fn ($q) => $q->where('id', '!=', $ignoreId))
                ->exists()
        ) {
            $slug = "{$base}-{$counter}";
            $counter++;
        }

        return $slug;
    }

    /**
     * İşçi tipinin default və oyun-əsaslı vizual bonus xal qaydalarını yeniləyir.
     */
    public function syncBonusRules(WorkerType $workerType, ?float $defaultPoints, array $gameRules = []): void
    {
        DB::transaction(function () use ($workerType, $defaultPoints, $gameRules) {
            if (!$workerType->is_bonus_eligible) {
                $workerType->bonusRules()->delete();
                return;
            }

            if ($defaultPoints !== null) {
                $defaultRule = $workerType->bonusRules()->whereNull('portal_game_id')->first();
                if ($defaultRule) {
                    $defaultRule->update([
                        'points' => round($defaultPoints, 2),
                        'is_active' => true,
                    ]);
                } else {
                    WorkerTypeBonusRule::create([
                        'worker_type_id' => $workerType->id,
                        'portal_game_id' => null,
                        'game_name' => null,
                        'points' => round($defaultPoints, 2),
                        'is_active' => true,
                    ]);
                }
            } else {
                $workerType->bonusRules()->whereNull('portal_game_id')->delete();
            }

            $keptGameIds = [];
            foreach ($gameRules as $rule) {
                $gameId = !empty($rule['portal_game_id']) ? (int) $rule['portal_game_id'] : null;
                if (!$gameId) {
                    continue;
                }

                $points = isset($rule['points']) && $rule['points'] !== '' ? (float) $rule['points'] : null;
                if ($points === null) {
                    continue;
                }

                $keptGameIds[] = $gameId;

                WorkerTypeBonusRule::updateOrCreate(
                    [
                        'worker_type_id' => $workerType->id,
                        'portal_game_id' => $gameId,
                    ],
                    [
                        'game_name' => trim((string) ($rule['game_name'] ?? '')) ?: "Oyun #{$gameId}",
                        'points' => round($points, 2),
                        'is_active' => array_key_exists('is_active', $rule) ? (bool) $rule['is_active'] : true,
                    ]
                );
            }

            $workerType->bonusRules()
                ->whereNotNull('portal_game_id')
                ->when(!empty($keptGameIds), fn ($q) => $q->whereNotIn('portal_game_id', $keptGameIds))
                ->delete();
        });
    }

    /**
     * Dəyişiklikləri mövcud PortalWebsite cədvəllərinə (varsa) best-effort sinxron edir.
     * PortalWebsite DB əlçatan olmadıqda (məs. test/offline mühit) xəta atmır.
     */
    public function syncToPortalWebsite(WorkerType $workerType): void
    {
        try {
            $pw = DB::connection('portal_website');

            if ($workerType->portal_worker_type_id) {
                $pw->table('worker_types')
                    ->where('id', $workerType->portal_worker_type_id)
                    ->update([
                        'name' => $workerType->name,
                        'updated_at' => now(),
                    ]);
            } elseif ($workerType->is_active) {
                $existing = $pw->table('worker_types')
                    ->where('name', $workerType->name)
                    ->first();

                if ($existing) {
                    $workerType->updateQuietly(['portal_worker_type_id' => $existing->id]);
                } else {
                    $newId = $pw->table('worker_types')->insertGetId([
                        'name' => $workerType->name,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);
                    $workerType->updateQuietly(['portal_worker_type_id' => $newId]);
                }
            }

            if ($workerType->portal_worker_type_id && $workerType->is_bonus_eligible) {
                $targetPortalTypeIds = [$workerType->portal_worker_type_id];

                // Cinsiyyətə görə Aktrisa (ID=9) tipi də varsa, eyni xal qaydasını ona da ötürürük
                if (!empty($workerType->female_name)) {
                    $femalePortalType = $pw->table('worker_types')
                        ->where('name', $workerType->female_name)
                        ->first();
                    if ($femalePortalType) {
                        $targetPortalTypeIds[] = (int) $femalePortalType->id;
                    }
                }

                $rules = $workerType->bonusRules()->where('is_active', true)->get();

                foreach ($targetPortalTypeIds as $ptId) {
                    $pw->table('bonus_rules')->where('worker_type_id', $ptId)->delete();
                    foreach ($rules as $rule) {
                        $pw->table('bonus_rules')->insert([
                            'worker_type_id' => $ptId,
                            'game_id' => $rule->portal_game_id,
                            'points' => $rule->points,
                            'created_at' => now(),
                            'updated_at' => now(),
                        ]);
                    }
                }
            }
        } catch (\Throwable $e) {
            Log::info('WorkerType PortalWebsite sync skipped: ' . $e->getMessage());
        }
    }
}
