<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PerformanceInsight extends Model
{
    protected $fillable = [

        'member_id',
        'period_id',
        'communication_score',
        'story_point_score',
        'workload_score',
        'overall_score',
        'recommendation',
        'admin_notes',
        'is_sent',
        'sent_at'

    ];

    public function member()
    {
        return $this->belongsTo(Member::class);
    }

    public function period()
    {
        return $this->belongsTo(Period::class);
    }
    
}