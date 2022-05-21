<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MagazineCategory extends Model
{
    const STATUS_NOT_ACTIVE = 0;
    const STATUS_ACTIVE = 1;

    const TYPE_ARTICLE = 1;
    const TYPE_EVENT = 2;

    protected $table = 'magazine_categories';

    protected $fillable = [
        'name', 'slug', 'image', 'banner_image', 'short_description', 'description', 'type', 'is_active'
    ];

    public $timestamps = false;

    public function events()
    {
        return $this->hasMany('App\Models\Magazine', 'mag_category_id', 'id')
            ->where('type', Magazine::TYPE_EVENT);
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', '=', self::STATUS_ACTIVE);
    }
}
