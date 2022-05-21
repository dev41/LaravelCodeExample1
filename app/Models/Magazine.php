<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Magazine extends Model
{
    const STATUS_NOT_ACTIVE = 0;
    const STATUS_ACTIVE = 1;

    const NOT_DELETED = 0;
    const DELETED = 1;

    const NOT_FEATURED = 0;
    const FEATURED = 1;

    const TYPE_ARTICLE = 1;
    const TYPE_EVENT = 2;

    protected $table = 'magazines';

    protected $fillable = [
        'mag_category_id', 'title', 'slug', 'author_name', 'description', 'short_desc', 'user_id', 'image',
        'attach_file', 'article_image', 'type', 'auther_email', 'street', 'city', 'zipcode', 'event_start_time',
        'event_end_time', 'phone_no', 'tags', 'price', 'created_on', 'c_date', 'is_delete', 'is_featured', 'is_active'
    ];

    public $timestamps = false;

    public function category()
    {
        return $this->belongsTo('App\Models\MagazineCategory', 'mag_category_id', 'id');
    }

    public function user()
    {
        return $this->belongsTo('App\User', 'user_id', 'id');
    }

    public function eventImages()
    {
        return $this->hasMany('App\Models\EventImage', 'event_id', 'id');
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', '=', self::STATUS_ACTIVE);
    }
}
