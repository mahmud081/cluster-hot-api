<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MqttAccessLog extends Model
{
    protected $fillable = [
        'device_id',
        'email',
        'req_type'
    ];
}
