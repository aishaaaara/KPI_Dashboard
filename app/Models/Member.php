<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Member extends Model
{
    protected $fillable = [

        'eid',
        'user_id',
        'name',
        'position_id',
        'team_id',
        'employment_type_id',
        'join_date',
        'end_date'

    ];

    public function position()
    {
        return $this->belongsTo(Position::class);
    }

    public function team()
    {
        return $this->belongsTo(Team::class);
    }

    public function employmentType()
    {
        return $this->belongsTo(EmploymentType::class);
    }

        public function user()
    {
        return $this->belongsTo(User::class);
    }
    
    protected static function boot()
{
    parent::boot();

    static::creating(function ($member) {
        if (empty($member->eid)) {
            $lastEid = static::orderByRaw('CAST(SUBSTRING(eid, 4) AS UNSIGNED) DESC')
                ->value('eid');

            $nextNumber = $lastEid ? (int) substr($lastEid, 3) + 1 : 1;

            $member->eid = 'EMP' . str_pad($nextNumber, 3, '0', STR_PAD_LEFT);
        }
    });
}
    
}