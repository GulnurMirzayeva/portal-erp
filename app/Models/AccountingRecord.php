<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AccountingRecord extends Model
{
    protected $fillable = [
        'branch_id',
        'month',
        'sales_data',
        'summary_data',
        'updated_by',
    ];

    protected $casts = [
        'sales_data' => 'array',
        'summary_data' => 'array',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'updated_by');
    }
}
