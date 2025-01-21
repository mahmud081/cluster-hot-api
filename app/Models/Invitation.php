<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Invitation extends Model
{
    protected $fillable = [
        'sharer_id',
        'sharee_id',
        'accepted'
    ];

    protected $hidden = ['created_at', 'updated_at', 'pivot'];
}
