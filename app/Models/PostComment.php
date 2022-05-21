<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PostComment extends Model
{
    const IS_BLOCKED = 1;
    const IS_NOT_BLOCKED = 0;

    const IS_DELETED = 1;
    const IS_NOT_DELETED = 0;

    protected $table = 'post_comment';

    protected $fillable = [
        'comment_id', 'post_id', 'user_id', 'comment', 'is_delete', 'is_blocked', 'c_date'
    ];

    public $timestamps = false;

    /**
     * Get the author of the comment.
     */
    public function user()
    {
        return $this->hasOne('App\User', 'id', 'user_id');
    }

    /**
     * Get the post for the comment.
     */
    public function post()
    {
        return $this->belongsTo('App\Models\Post', 'post_id', 'id');
    }

    /**
     * Get the replies for the comment.
     */
    public function replies()
    {
        return $this->hasMany('App\Models\PostComment', 'comment_id', 'id');
    }

    /**
     * Get the links for the comment.
     */
    public function links()
    {
        return $this->hasMany('App\Models\CommentLink', 'comment_id', 'id');
    }

    /**
     * Get the likes for the comment.
     */
    public function likes()
    {
        return $this->hasMany('App\Models\PostLike', 'post_id', 'id')
            ->where('type', PostLike::TYPE_COMMENT_LIKE);
    }

    public function setCommentAttribute($value)
    {
        $value = str_replace('&nbsp;', ' ', $value);
        $value = html_entity_decode($value);
        $value = strip_tags($value, '<br>');
        $value = trim($value);
        $value = str_ireplace('<br>', PHP_EOL, $value);
        $value = preg_replace('/^[\n|\n\s]+|[\n|\n\s]+$/', '', $value);

        $this->attributes['comment'] = $value;
    }
}
