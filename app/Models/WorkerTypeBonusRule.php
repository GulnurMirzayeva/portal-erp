<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class WorkerTypeBonusRule extends Model
{
    protected $fillable = [
        'worker_type_id',
        'portal_game_id',
        'game_name',
        'points',
        'is_active',
        'portal_bonus_rule_id',
    ];

    protected function casts(): array
    {
        return [
            'worker_type_id' => 'integer',
            'portal_game_id' => 'integer',
            'points' => 'float',
            'is_active' => 'boolean',
            'portal_bonus_rule_id' => 'integer',
        ];
    }

    public function workerType(): BelongsTo
    {
        return $this->belongsTo(WorkerType::class, 'worker_type_id');
    }
}

