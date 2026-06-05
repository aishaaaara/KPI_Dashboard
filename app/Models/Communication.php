<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Communication extends Model
{
    protected $fillable = [

        'member_id',
        'period_id',
        'clarity',
        'responsiveness',
        'collaboration',
        'overall_score',
        'notes'

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