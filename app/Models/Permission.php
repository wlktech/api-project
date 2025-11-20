<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Permission extends Model
{
    protected $fillable = [
        'name', 
        'feature_id', 
    ];

    public function feature()
    {
        return $this->belongsTo(Feature::class);
    }
}
