<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Device extends Model
{
    protected $fillable = [
        'user_id',
        'name',
        'property_id',
        'room_id',
        'network_id',
        'mqtt_id',
        'type',
        'mac',
        'ip_address',
        'position',
        'max_value'
    ];

    protected $hidden = ['created_at', 'updated_at', 'pivot'];
}
