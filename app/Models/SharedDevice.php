<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SharedDevice extends Model
{
    protected $fillable = [
        'sharer_id',
        'sharee_id',
        'device_id',
        'role'
    ];

    protected $hidden = ['created_at', 'updated_at', 'pivot'];
}
