<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\WorkerType;
use App\Services\WorkerTypeService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ErpWorkerTypeApiController extends Controller
{
    public function __construct(
        protected WorkerTypeService $workerTypeService
    ) {}

    /**
     * PortalWebsite və digər modullar üçün aktiv işçi tiplərini və vizual bonus qaydalarını qaytarır.
     */
    public function index(Request $request): JsonResponse
    {
        $query = WorkerType::with(['bonusRules' => fn ($q) => $q->where('is_active', true)])
            ->ordered();

        if (!$request->boolean('include_inactive', false)) {
            $query->where('is_active', true);
        }

        if ($request->filled('scope')) {
            $scope = $request->input('scope');
            if ($scope === WorkerType::SCOPE_BRANCH) {
                $query->branch();
            } elseif ($scope === WorkerType::SCOPE_OFFICE) {
                $query->office();
            }
        }

        if ($request->boolean('bonus_only', false)) {
            $query->where('is_bonus_eligible', true);
        }

        $types = $query->get()->map(function (WorkerType $type) {
            $defaultRule = $type->bonusRules->firstWhere('portal_game_id', null);
            $gameOverrides = $type->bonusRules
                ->whereNotNull('portal_game_id')
                ->values()
                ->map(fn ($r) => [
                    'portal_game_id' => (int) $r->portal_game_id,
                    'game_name' => $r->game_name,
                    'points' => (float) $r->points,
                ]);

            return [
                'id' => $type->id,
                'portal_worker_type_id' => $type->portal_worker_type_id,
                'name' => $type->name,
                'female_name' => $type->female_name,
                'slug' => $type->slug,
                'short_code' => $type->short_code,
                'female_short_code' => $type->female_short_code,
                'scope' => $type->scope,
                'category' => $type->category,
                'is_bonus_eligible' => $type->is_bonus_eligible,
                'can_have_intern' => $type->can_have_intern,
                'requires_game' => $type->requires_game,
                'show_in_tabel' => $type->show_in_tabel,
                'can_substitute' => $type->can_substitute,
                'sale_slot_key' => $type->sale_slot_key,
                'tabel_sort_order' => $type->tabel_sort_order,
                'default_pay_model' => $type->default_pay_model,
                'badge_color' => $type->badge_color,
                'is_active' => $type->is_active,
                'visual_bonus' => [
                    'default_points' => $defaultRule ? (float) $defaultRule->points : null,
                    'game_overrides' => $gameOverrides,
                ],
            ];
        });

        return response()->json([
            'success' => true,
            'worker_types' => $types,
        ]);
    }

    /**
     * Seçilmiş rollar, oyun və stajor statusuna görə vizual yarış xalını hesablayır.
     */
    public function calculateVisualBonus(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'worker_type_ids' => 'required|array',
            'worker_type_ids.*' => 'integer',
            'game_id' => 'nullable|integer',
            'is_intern' => 'boolean',
        ]);

        $result = $this->workerTypeService->resolveVisualPoints(
            $validated['worker_type_ids'],
            isset($validated['game_id']) ? (int) $validated['game_id'] : null,
            (bool) ($validated['is_intern'] ?? false)
        );

        return response()->json([
            'success' => true,
            'result' => $result,
        ]);
    }
}

