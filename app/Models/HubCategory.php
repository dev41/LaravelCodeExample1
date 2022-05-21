<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class HubCategory extends Model
{
    const IS_NOT_ACTIVE = 0;
    const IS_ACTIVE = 1;

    protected $table = 'hub_categories';

    protected $fillable = [
        'title', 'status'
    ];

    public function hubs()
    {
        return $this->hasMany('App\Models\Hub', 'category_id', 'id');
    }

    public function scopeActive($query)
    {
        return $query->where('status', '=', self::IS_ACTIVE);
    }
}
