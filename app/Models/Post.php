<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Post extends Model
{
    const TYPE_PUBLIC = 1;
    const TYPE_WITH_CONNECTIONS = 2;
    const TYPE_ANONYMOUS = 3;

    const STATUS_ACTIVE = 1;
    const STATUS_NOT_ACTIVE = 0;

    const IS_BLOCKED = 1;
    const IS_NOT_BLOCKED = 0;

    const GROUP_TYPE_SOCIAL_POST = 1;
    const GROUP_TYPE_GROUP_POST = 2;
    const GROUP_TYPE_COMPANY_POST = 3;
    const GROUP_TYPE_HUB_POST = 4;
    const GROUP_TYPE_ANOTHER_USER = 5;

    const ANONYMOUS_USER_TITLE = 'Anonymous';

    protected $table = 'posts';

    protected $fillable = [
        'group_id', 'user_id', 'title', 'description', 'file_name', 'post_type', 'group_type', 'created_date',
        'is_blocked', 'is_active'
    ];

    public $timestamps = false;

    /**
     * Get the comments for the post.
     */
    public function comments()
    {
        return $this->hasMany('App\Models\PostComment', 'post_id', 'id')
            ->whereNull('comment_id');
    }

    /**
     * Get the likes for the post.
     */
    public function likes()
    {
        return $this->hasMany('App\Models\PostLike', 'post_id', 'id')
            ->where('type', PostLike::TYPE_POST_LIKE);
    }

    /**
     * Get the connections for the post.
     */
    public function connections()
    {
        return $this->hasMany('App\Models\PostConnection', 'post_id', 'id');
    }

    /**
     * Get the files for the post.
     */
    public function files()
    {
        return $this->hasMany('App\Models\PostImage', 'post_id', 'id')->where('v2', 0);
    }

    /**
     * Get the files for the post.
     */
    public function images()
    {
        return $this->hasMany('App\Models\File', 'object_id', 'id')
            ->whereIn('type', [
                File::TYPE_POSTS,
                File::TYPE_GROUP_POSTS,
                File::TYPE_ANOTHER_USER_POSTS
            ]);
    }

    /**
     * Get the link for the post.
     */
    public function links()
    {
        return $this->hasOne('App\Models\PostLink', 'post_id', 'id');
    }

    /**
     * Get the user for the post.
     */
    public function user()
    {
        return $this->belongsTo('App\User', 'user_id', 'id');
    }

    public function scopeNotBlocked($query)
    {
        return $query->where('is_blocked', '=', self::IS_NOT_BLOCKED);
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', '=', self::STATUS_ACTIVE);
    }

    public function setDescriptionAttribute($value)
    {
        $value = str_replace('&nbsp;', ' ', $value);
        $value = html_entity_decode($value);
        $value = strip_tags($value, '<br>');
        $value = trim($value);
        $value = str_ireplace('<br>', PHP_EOL, $value);
        $value = preg_replace('/^[\n|\n\s]+|[\n|\n\s]+$/', '', $value);

        $this->attributes['description'] = $value;
    }
}
