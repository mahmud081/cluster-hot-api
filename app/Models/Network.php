<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Network extends Model
{
    protected $fillable = [
        'user_id',
        'ssid',
        'property_id',
        'password',
        'mac_id'
    ];

    protected $hidden = ['created_at', 'updated_at'];

    public function groups()
    {
        return $this->hasManyThrough(GroupDevice::class, Device::class);
    }

    public function devices()
    {
        return $this->hasMany(Device::class, 'network_id');
    }
}
