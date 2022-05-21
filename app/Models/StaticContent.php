<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StaticContent extends Model
{
    const NOT_ACTIVE = 0;
    const ACTIVE = 1;

    protected $table = 'static_contents';

    protected $fillable = [
        'title', 'page_heading', 'slug', 'short_desc', 'banner_image', 'image', 'parent_id', 'is_active', 'create_date'
    ];

    public $timestamps = false;
}
