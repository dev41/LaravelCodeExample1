<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PostLike extends Model
{
    const TYPE_POST_LIKE = 1;
    const TYPE_COMMENT_LIKE = 2;

    protected $table = 'post_like';

    protected $fillable = [
        'post_id', 'user_id', 'type', 'c_date'
    ];

    public $timestamps = false;

    public function post()
    {
        return $this->belongsTo('App\Models\Post', 'post_id', 'id')->join('post_like', function ($join) {
            $join->on('post_like.post_id', '=', 'posts.id')
                ->where('post_like.type', self::TYPE_POST_LIKE);
        })->select(['posts.*']);
    }

    public function comment()
    {
        return $this->belongsTo('App\Models\PostComment', 'post_id', 'id')->join('post_like', function ($join) {
            $join->on('post_like.post_id', '=', 'post_comment.id')
                ->where('post_like.type', self::TYPE_COMMENT_LIKE);
        })->select(['post_comment.*']);
    }

    public function user()
    {
        return $this->belongsTo('App\User', 'user_id', 'id');
    }
}
