<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class CrmTrafficStatistic extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'crm_traffic_statistics';

    protected $fillable = [
        'date',
        'year',
        'month',
        'day',
        'instagram_messages',
        'instagram_followers_new',
        'instagram_followers_lost',
        'instagram_followers_net',
        'calls',
        'tiktok_followers_net',
        'website_users',
        'website_visits',
        'notes',
        'created_by',
        'updated_by',
    ];

    protected $casts = [
        'date' => 'date',
        'year' => 'integer',
        'month' => 'integer',
        'day' => 'integer',
        'instagram_messages' => 'integer',
        'instagram_followers_new' => 'integer',
        'instagram_followers_lost' => 'integer',
        'instagram_followers_net' => 'integer',
        'calls' => 'integer',
        'tiktok_followers_net' => 'integer',
        'website_users' => 'integer',
        'website_visits' => 'integer',
    ];

    /**
     * İnstaqram xalis izləyici artımını hesabla (Yeni - Çıxan)
     */
    public function recalculateInstagramNet(): int
    {
        $this->instagram_followers_net = (int)$this->instagram_followers_new - (int)$this->instagram_followers_lost;
        return $this->instagram_followers_net;
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
