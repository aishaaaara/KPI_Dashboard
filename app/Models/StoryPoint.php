<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StoryPoint extends Model
{
    protected $fillable = [

    'member_id',

    'period_id',

    'target',

    'totals',

    'summary'

];

public function member()
{
    return $this->belongsTo(
        Member::class
    );
}

public function period()
{
    return $this->belongsTo(
        Period::class
    );
}
}