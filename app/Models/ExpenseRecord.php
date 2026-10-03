<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ExpenseRecord extends Model
{
    protected $fillable = [
        'branch_id',
        'month',
        'expenses_data',
        'summary_data',
        'updated_by',
    ];

    protected $casts = [
        'expenses_data' => 'array',
        'summary_data' => 'array',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'updated_by');
    }
}
