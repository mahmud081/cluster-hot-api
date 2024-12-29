<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Group extends Model
{
    protected $fillable = [
        'user_id',
        'name',
    ];

    protected $hidden = ['created_at', 'updated_at', 'pivot'];

    public function devices()
    {
        return $this->belongsToMany(Device::class, 'groups_devices');
    }
}
