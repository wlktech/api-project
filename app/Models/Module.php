<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Module extends Model
{
    protected $fillable = [
        'name',
        'slug',
        'is_active',
    ];

    public function features()
    {
        return $this->hasMany(Feature::class);
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }
}
