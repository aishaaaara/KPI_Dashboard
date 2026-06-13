<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;


class RegisterRequest extends Model
{
protected $fillable = [

    'name',

    'email',

    'password',

    'status',

    'member_id',

    'approved_by',

    'approved_at',

    'rejection_reason'

];

    public function member()
{
    return $this->belongsTo(
        \App\Models\Member::class,
        'member_id'
    );
}
}
