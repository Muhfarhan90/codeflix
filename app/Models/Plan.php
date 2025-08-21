<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Plan extends Model
{
    protected $fillable = [
        'title',
        'price',
        'duration',
        'resolution',
        'max_devices'
    ];

    public function memberships()
    {
        return $this->hasMany(Membership::class);
    }

    public function users() {
        return $this->belongsToMany(User::class, 'membership', 'plan_id', 'user_id');
    }
}
