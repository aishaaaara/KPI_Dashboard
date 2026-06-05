<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Workload extends Model
{
    protected $fillable = [
        'member_id',
        'period_id',
        'all_task',
        'todo',
        'progress',
        'review',
        'done',
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
