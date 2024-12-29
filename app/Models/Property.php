<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Property extends Model
{
    protected $fillable = [
        'user_id',
        'name'
    ];

    protected $hidden = ['created_at', 'updated_at'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function rooms()
    {
        return $this->hasMany(Room::class, 'property_id');
    }

    public function networks()
    {
        return $this->hasMany(Network::class, 'property_id');
    }

    public function devices()
    {
        return $this->hasMany(Device::class, 'property_id');
    }
}
