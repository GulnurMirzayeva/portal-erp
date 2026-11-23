<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class WorkerType extends Model
{
    public const SCOPE_BRANCH = 'branch';
    public const SCOPE_OFFICE = 'office';
    public const SCOPE_BOTH = 'both';

    public const CATEGORY_OPERATIONAL = 'operational';
    public const CATEGORY_MANAGEMENT = 'management';
    public const CATEGORY_SUPPORT = 'support';

    public const PAY_MODELS = [
        'per_shift' => 'Smenbaşı',
        'fixed_monthly' => 'Aylıq Fix',
        'per_game' => 'Oyunbaşı',
        'hybrid' => 'Hibrid (Fix/Smen + Oyun)',
        'none' => 'Təyin edilməyib',
    ];

    public const SALE_SLOTS = [
        'receptionists' => 'Resepşn (receptionists)',
        'actors' => 'Aktyor / Aktrisa (actors)',
        'operators' => 'Operator (operators)',
        'hostesses' => 'Hostes (hostesses)',
    ];

    protected $fillable = [
        'portal_worker_type_id',
        'name',
        'female_name',
        'slug',
        'short_code',
        'female_short_code',
        'scope',
        'category',
        'is_bonus_eligible',
        'can_have_intern',
        'requires_game',
        'show_in_tabel',
        'can_substitute',
        'sale_slot_key',
        'tabel_sort_order',
        'default_pay_model',
        'badge_color',
        'description',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'portal_worker_type_id' => 'integer',
            'is_bonus_eligible' => 'boolean',
            'can_have_intern' => 'boolean',
            'requires_game' => 'boolean',
            'show_in_tabel' => 'boolean',
            'can_substitute' => 'boolean',
            'tabel_sort_order' => 'integer',
            'is_active' => 'boolean',
        ];
    }

    public function bonusRules(): HasMany
    {
        return $this->hasMany(WorkerTypeBonusRule::class, 'worker_type_id');
    }

    public function defaultBonusRule(): HasOne
    {
        return $this->hasOne(WorkerTypeBonusRule::class, 'worker_type_id')
            ->whereNull('portal_game_id');
    }

    public function gameBonusRules(): HasMany
    {
        return $this->hasMany(WorkerTypeBonusRule::class, 'worker_type_id')
            ->whereNotNull('portal_game_id')
            ->orderBy('game_name');
    }

    public function scopeActive(Builder $query): Builder
    {
        return $query->where('is_active', true);
    }

    public function scopeBranch(Builder $query): Builder
    {
        return $query->whereIn('scope', [self::SCOPE_BRANCH, self::SCOPE_BOTH]);
    }

    public function scopeOffice(Builder $query): Builder
    {
        return $query->whereIn('scope', [self::SCOPE_OFFICE, self::SCOPE_BOTH]);
    }

    public function scopeBonusEligible(Builder $query): Builder
    {
        return $query->where('is_bonus_eligible', true);
    }

    public function scopeInternEligible(Builder $query): Builder
    {
        return $query->where('can_have_intern', true);
    }

    public function scopeOrdered(Builder $query): Builder
    {
        return $query->orderBy('tabel_sort_order')->orderBy('name');
    }

    /**
     * Cinsiyyətə görə tam adı qaytarır (məs: Aktyor / Aktrisa).
     */
    public function getNameForGender(string $gender = 'male'): string
    {
        if ($gender === 'female' && !empty($this->female_name)) {
            return $this->female_name;
        }

        return $this->name;
    }

    /**
     * Cinsiyyətə görə tabel qısa kodunu qaytarır.
     */
    public function getShortCodeForGender(string $gender = 'male'): string
    {
        if ($gender === 'female' && !empty($this->female_short_code)) {
            return $this->female_short_code;
        }

        return $this->short_code;
    }

    /**
     * Cinsiyyət, stajor və əvəzedici statusuna görə tam oxunaqlı vəzifə adını qaytarır.
     */
    public function getDisplayTitle(string $gender = 'male', bool $isIntern = false, bool $isSubstitute = false): string
    {
        $base = $this->getNameForGender($gender);

        if ($isIntern) {
            $base = 'Stajor - ' . $base;
        }

        if ($isSubstitute) {
            $base = 'Əvəz ' . mb_strtolower($base);
        }

        return $base;
    }

    /**
     * Bu vəzifə üçün verilmiş oyuna uyğun vizual yarış xalını hesablayır.
     */
    public function resolvePointsForGame(?int $portalGameId = null, bool $isIntern = false): float
    {
        if ($isIntern || !$this->is_active || !$this->is_bonus_eligible) {
            return 0.0;
        }

        $rules = $this->relationLoaded('bonusRules')
            ? $this->bonusRules
            : $this->bonusRules()->get();

        if ($portalGameId !== null) {
            $gameRule = $rules->first(function (WorkerTypeBonusRule $rule) use ($portalGameId) {
                return $rule->is_active && (int) $rule->portal_game_id === (int) $portalGameId;
            });

            if ($gameRule) {
                return (float) $gameRule->points;
            }
        }

        $defaultRule = $rules->first(function (WorkerTypeBonusRule $rule) {
            return $rule->is_active && $rule->portal_game_id === null;
        });

        return $defaultRule ? (float) $defaultRule->points : 0.0;
    }

    public function getScopeLabelAttribute(): string
    {
        return match ($this->scope) {
            self::SCOPE_BRANCH => 'Filial',
            self::SCOPE_OFFICE => 'Ofis',
            self::SCOPE_BOTH => 'Filial & Ofis',
            default => $this->scope,
        };
    }

    public function getCategoryLabelAttribute(): string
    {
        return match ($this->category) {
            self::CATEGORY_OPERATIONAL => 'Bonus Gətirən (Satış)',
            self::CATEGORY_MANAGEMENT => 'İdarəedici',
            self::CATEGORY_SUPPORT => 'Dəstək / İnzibati',
            default => $this->category,
        };
    }

    public function getPayModelLabelAttribute(): string
    {
        return self::PAY_MODELS[$this->default_pay_model] ?? $this->default_pay_model;
    }
}

