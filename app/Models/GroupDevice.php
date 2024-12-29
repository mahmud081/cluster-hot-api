<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class GroupDevice extends Model
{
    protected $fillable = [
        'user_id',
        'group_id',
        'device_id'
    ];

    protected $hidden = ['created_at', 'updated_at'];
}
