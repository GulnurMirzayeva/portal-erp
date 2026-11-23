<?php

namespace App\Http\Controllers;

use App\Models\WorkerType;
use App\Services\PortalWebsiteService;
use App\Services\WorkerTypeService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;

class WorkerTypeController extends Controller
{
    public function __construct(
        protected WorkerTypeService $workerTypeService,
        protected PortalWebsiteService $portalService
    ) {}

    /**
     * İşçi tiplərinin və dinamik qaydaların siyahısı.
     */
    public function index(Request $request)
    {
        $scope = $request->input('scope', 'all');
        $category = $request->input('category', 'all');
        $status = $request->input('status', 'all');
        $search = trim((string) $request->input('search', ''));

        $query = WorkerType::with(['defaultBonusRule', 'gameBonusRules'])->ordered();

        if ($scope === WorkerType::SCOPE_BRANCH) {
            $query->branch();
        } elseif ($scope === WorkerType::SCOPE_OFFICE) {
            $query->office();
        }

        if (in_array($category, [
            WorkerType::CATEGORY_OPERATIONAL,
            WorkerType::CATEGORY_MANAGEMENT,
            WorkerType::CATEGORY_SUPPORT,
        ], true)) {
            $query->where('category', $category);
        }

        if ($status === 'active') {
            $query->where('is_active', true);
        } elseif ($status === 'inactive') {
            $query->where('is_active', false);
        }

        if ($search !== '') {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('female_name', 'like', "%{$search}%")
                  ->orWhere('short_code', 'like', "%{$search}%")
                  ->orWhere('female_short_code', 'like', "%{$search}%");
            });
        }

        $workerTypes = $query->get();

        $allStats = WorkerType::query();
        $stats = [
            'total' => (clone $allStats)->count(),
            'branch' => (clone $allStats)->branch()->count(),
            'office' => (clone $allStats)->office()->count(),
            'bonus_eligible' => (clone $allStats)->where('is_bonus_eligible', true)->count(),
            'intern_eligible' => (clone $allStats)->where('can_have_intern', true)->count(),
            'management' => (clone $allStats)->where('category', WorkerType::CATEGORY_MANAGEMENT)->count(),
        ];

        return view('worker_types.index', compact(
            'workerTypes',
            'stats',
            'scope',
            'category',
            'status',
            'search'
        ));
    }

    /**
     * Yeni işçi tipi yaratma forması.
     */
    public function create()
    {
        $nextOrder = ((int) WorkerType::max('tabel_sort_order')) + 10;
        $games = $this->portalService->getAllGames();

        return view('worker_types.create', [
            'workerType' => new WorkerType([
                'scope' => WorkerType::SCOPE_BRANCH,
                'category' => WorkerType::CATEGORY_OPERATIONAL,
                'is_bonus_eligible' => true,
                'can_have_intern' => true,
                'requires_game' => false,
                'show_in_tabel' => true,
                'can_substitute' => true,
                'tabel_sort_order' => $nextOrder > 10 ? $nextOrder : 10,
                'default_pay_model' => 'per_shift',
                'badge_color' => '#107c41',
                'is_active' => true,
            ]),
            'games' => $games,
            'defaultPoints' => null,
            'gameRules' => [],
        ]);
    }

    /**
     * Yeni işçi tipini və bonus qaydalarını saxlayır.
     */
    public function store(Request $request): RedirectResponse
    {
        [$attributes, $defaultPoints, $gameRules] = $this->validateAndPrepare($request);

        $workerType = DB::transaction(function () use ($attributes, $defaultPoints, $gameRules) {
            $attributes['slug'] = $this->workerTypeService->generateUniqueSlug($attributes['name']);
            $type = WorkerType::create($attributes);
            $this->workerTypeService->syncBonusRules($type, $defaultPoints, $gameRules);
            return $type;
        });

        $this->workerTypeService->syncToPortalWebsite($workerType);

        return redirect()->route('worker-types.index')
            ->with('success', "«{$workerType->name}» işçi tipi uğurla yaradıldı.");
    }

    /**
     * Mövcud işçi tipini redaktə etmə forması.
     */
    public function edit(WorkerType $workerType)
    {
        $workerType->load(['defaultBonusRule', 'gameBonusRules']);
        $games = $this->portalService->getAllGames();

        $defaultPoints = $workerType->defaultBonusRule?->points;
        $gameRules = $workerType->gameBonusRules->map(fn ($r) => [
            'portal_game_id' => $r->portal_game_id,
            'game_name' => $r->game_name,
            'points' => $r->points,
            'is_active' => $r->is_active,
        ])->values()->all();

        return view('worker_types.edit', compact(
            'workerType',
            'games',
            'defaultPoints',
            'gameRules'
        ));
    }

    /**
     * İşçi tipini və bonus qaydalarını yeniləyir.
     */
    public function update(Request $request, WorkerType $workerType): RedirectResponse
    {
        [$attributes, $defaultPoints, $gameRules] = $this->validateAndPrepare($request, $workerType->id);

        DB::transaction(function () use ($workerType, $attributes, $defaultPoints, $gameRules) {
            $workerType->update($attributes);
            $this->workerTypeService->syncBonusRules($workerType, $defaultPoints, $gameRules);
        });

        $this->workerTypeService->syncToPortalWebsite($workerType->fresh());

        return redirect()->route('worker-types.index')
            ->with('success', "«{$workerType->name}» işçi tipi və qaydaları yeniləndi.");
    }

    /**
     * İşçi tipinin aktiv/deaktiv statusunu dəyişir.
     */
    public function toggleStatus(WorkerType $workerType): JsonResponse
    {
        $workerType->update(['is_active' => !$workerType->is_active]);

        return response()->json([
            'success' => true,
            'is_active' => $workerType->is_active,
            'message' => "«{$workerType->name}» statusu yeniləndi.",
        ]);
    }

    /**
     * İşçi tipini silir.
     */
    public function destroy(WorkerType $workerType): RedirectResponse
    {
        $name = $workerType->name;
        $workerType->delete();

        return redirect()->route('worker-types.index')
            ->with('success', "«{$name}» işçi tipi silindi.");
    }

    /**
     * Standart işçi tiplərini və ilkin bonus qaydalarını bazaya doldurur.
     */
    public function seedDefaults(): RedirectResponse
    {
        $result = $this->workerTypeService->seedDefaults();

        return redirect()->route('worker-types.index')
            ->with(
                'success',
                "Standart tiplər yoxlanıldı: {$result['created_types']} yeni tip və {$result['created_rules']} ilkin bonus qaydası əlavə edildi."
            );
    }

    /**
     * Həm store, həm update üçün ümumi validasiya və biznes qaydası yoxlaması.
     */
    private function validateAndPrepare(Request $request, ?int $ignoreId = null): array
    {
        $validated = $request->validate([
            'name' => [
                'required',
                'string',
                'max:120',
                Rule::unique('worker_types', 'name')->ignore($ignoreId),
            ],
            'female_name' => 'nullable|string|max:120',
            'short_code' => 'required|string|max:30',
            'female_short_code' => 'nullable|string|max:30',
            'scope' => ['required', Rule::in([WorkerType::SCOPE_BRANCH, WorkerType::SCOPE_OFFICE, WorkerType::SCOPE_BOTH])],
            'category' => ['required', Rule::in([WorkerType::CATEGORY_OPERATIONAL, WorkerType::CATEGORY_MANAGEMENT, WorkerType::CATEGORY_SUPPORT])],
            'sale_slot_key' => ['nullable', Rule::in(array_keys(WorkerType::SALE_SLOTS))],
            'tabel_sort_order' => 'required|integer|min:1|max:9999',
            'default_pay_model' => ['required', Rule::in(array_keys(WorkerType::PAY_MODELS))],
            'badge_color' => ['nullable', 'regex:/^#[0-9a-fA-F]{6}$/'],
            'description' => 'nullable|string|max:1000',
            'default_points' => 'nullable|numeric|min:0|max:9999',
            'game_rules' => 'nullable|array',
            'game_rules.*.portal_game_id' => 'nullable|integer|min:1',
            'game_rules.*.game_name' => 'nullable|string|max:191',
            'game_rules.*.points' => 'nullable|numeric|min:0|max:9999',
        ], [
            'name.required' => 'Vəzifə adı mütləq daxil edilməlidir.',
            'name.unique' => 'Bu adda işçi tipi artıq mövcuddur.',
            'short_code.required' => 'Tabel üçün qısa kod mütləq daxil edilməlidir (məs: K, adm, res, akt).',
            'tabel_sort_order.required' => 'Tabel sıralama nömrəsi daxil edilməlidir.',
            'badge_color.regex' => 'Rəng kodu düzgün HEX formatında olmalıdır (məs: #107c41).',
        ]);

        $isBonusEligible = $request->boolean('is_bonus_eligible');
        $canHaveIntern = $request->boolean('can_have_intern');
        $requiresGame = $request->boolean('requires_game');
        $showInTabel = $request->boolean('show_in_tabel');
        $canSubstitute = $request->boolean('can_substitute');
        $isActive = $request->boolean('is_active');

        if ($canHaveIntern && (!$isBonusEligible || $validated['category'] !== WorkerType::CATEGORY_OPERATIONAL)) {
            throw ValidationException::withMessages([
                'can_have_intern' => 'Stajor yalnız bonus gətirən (oyunda/satışda iştirak edən) vəzifələr üzrə aktiv edilə bilər.',
            ]);
        }

        if ($isBonusEligible && $validated['category'] === WorkerType::CATEGORY_MANAGEMENT) {
            throw ValidationException::withMessages([
                'is_bonus_eligible' => 'İdarəedici vəzifə eyni zamanda satışda bonus gətirən ola bilməz. Bonus gətirən rol üçün kateqoriyanı "Bonus Gətirən" seçin.',
            ]);
        }

        $defaultPoints = $request->filled('default_points') ? (float) $validated['default_points'] : null;

        $gamesIndex = [];
        if ($request->filled('game_rules')) {
            foreach ($this->portalService->getAllGames() as $g) {
                $gamesIndex[(int) $g['id']] = $g['label'] ?? $g['name'];
            }
        }

        $gameRules = [];
        $seenGames = [];
        foreach ((array) $request->input('game_rules', []) as $idx => $row) {
            $gId = !empty($row['portal_game_id']) ? (int) $row['portal_game_id'] : null;
            $pts = $row['points'] ?? null;

            if (!$gId && ($pts === null || $pts === '')) {
                continue;
            }

            if (!$gId || $pts === null || $pts === '') {
                throw ValidationException::withMessages([
                    "game_rules.{$idx}.portal_game_id" => 'Oyun üzrə xüsusi qaydada həm oyun, həm də xal daxil edilməlidir.',
                ]);
            }

            if (isset($seenGames[$gId])) {
                throw ValidationException::withMessages([
                    "game_rules.{$idx}.portal_game_id" => 'Eyni oyun üçün birdən çox xüsusi bonus qaydası əlavə edilə bilməz.',
                ]);
            }
            $seenGames[$gId] = true;

            $gameRules[] = [
                'portal_game_id' => $gId,
                'game_name' => $gamesIndex[$gId] ?? trim((string) ($row['game_name'] ?? '')) ?: "Oyun #{$gId}",
                'points' => (float) $pts,
                'is_active' => !empty($row['is_active']),
            ];
        }

        $attributes = [
            'name' => trim($validated['name']),
            'female_name' => !empty($validated['female_name']) ? trim($validated['female_name']) : null,
            'short_code' => trim($validated['short_code']),
            'female_short_code' => !empty($validated['female_short_code']) ? trim($validated['female_short_code']) : null,
            'scope' => $validated['scope'],
            'category' => $validated['category'],
            'is_bonus_eligible' => $isBonusEligible,
            'can_have_intern' => $canHaveIntern,
            'requires_game' => $requiresGame,
            'show_in_tabel' => $showInTabel,
            'can_substitute' => $canSubstitute,
            'sale_slot_key' => $isBonusEligible ? ($validated['sale_slot_key'] ?? null) : null,
            'tabel_sort_order' => (int) $validated['tabel_sort_order'],
            'default_pay_model' => $validated['default_pay_model'],
            'badge_color' => $validated['badge_color'] ?? '#2563eb',
            'description' => !empty($validated['description']) ? trim($validated['description']) : null,
            'is_active' => $isActive,
        ];

        return [$attributes, $defaultPoints, $gameRules];
    }
}

