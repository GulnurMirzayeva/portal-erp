<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CrmInquiryStatistic extends Model
{
    use HasFactory;

    protected $table = 'crm_inquiry_statistics';

    protected $fillable = [
        'year',
        'month',
        'period_label',
        'cancellations',
        'discounts',
        'reservations',
        'location',
        'games',
        'working_hours',
        'complaints',
        'other',
        'total',
        'notes',
        'created_by',
        'updated_by',
    ];

    protected $casts = [
        'year' => 'integer',
        'month' => 'integer',
        'cancellations' => 'integer',
        'discounts' => 'integer',
        'reservations' => 'integer',
        'location' => 'integer',
        'games' => 'integer',
        'working_hours' => 'integer',
        'complaints' => 'integer',
        'other' => 'integer',
        'total' => 'integer',
    ];

    /**
     * Sətir cəmini avtomatik hesabla
     */
    public function calculateTotal(): int
    {
        return (int) $this->cancellations
            + (int) $this->discounts
            + (int) $this->reservations
            + (int) $this->location
            + (int) $this->games
            + (int) $this->working_hours
            + (int) $this->complaints
            + (int) $this->other;
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function updater()
    {
        return $this->belongsTo(User::class, 'updated_by');
    }
}
