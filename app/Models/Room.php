<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Room extends Model
{
    protected $fillable = [
        'user_id',
        'name',
        'property_id',
        'columns'
    ];

    protected $hidden = ['created_at', 'updated_at'];

    public function devices()
    {
        return $this->hasMany(Device::class, 'room_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
