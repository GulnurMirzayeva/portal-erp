<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class EmployeeStatusHistory extends Model
{
    protected $fillable = [
        'employee_id',
        'event_type',
        'old_value',
        'new_value',
        'effective_date',
        'note',
        'created_by_user_id',
    ];

    protected function casts(): array
    {
        return [
            'effective_date' => 'date',
            'old_value' => 'array',
            'new_value' => 'array',
        ];
    }

    public function employee(): BelongsTo
    {
        return $this->belongsTo(Employee::class);
    }

    public function createdByUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by_user_id');
    }
}
