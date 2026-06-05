<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EmploymentType extends Model
{
    protected $fillable = [
        'name'
    ];

    public function members()
    {
        return $this->hasMany(Member::class);
    }
}