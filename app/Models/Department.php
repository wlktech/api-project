<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Department extends Model
{
    protected $fillable = [
        'name',
        'code',
        'is_active'
    ];

    public function roles()
    {
        return $this->hasMany(Role::class);
    }
}
