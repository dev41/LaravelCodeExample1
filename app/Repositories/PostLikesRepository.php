<?php

namespace App\Repositories;

use App\Models\EmailTemplate;
use App\Models\Notification;
use App\Models\Post;
use App\Models\PostComment;
use App\Models\PostLike;
use App\Models\Settings;
use App\Repositories\Contracts\EmailTemplatesRepositoryInterface;
use App\Repositories\Contracts\NotificationsRepositoryInterface;
use App\Repositories\Contracts\PostCommentsRepositoryInterface;
use App\Repositories\Contracts\PostLikesRepositoryInterface;
use App\Repositories\Contracts\PostsRepositoryInterface;
use App\Traits\RepositoryTrait;
use App\User;

class PostLikesRepository implements PostLikesRepositoryInterface
{
    use RepositoryTrait;
    
    protected $model;

    public function __construct(PostLike $postLike)
    {
        $this->model = $postLike;
    }

    public function destroyAllByPostId($postId)
    {
        return $this->model->where([
            'post_id' => $postId
        ])->delete();
    }

    public function getUserCommentLikesCount(int $postId)
    {
        return $this->model
            ->leftJoin('users', 'post_like.user_id', '=', 'users.id')
            ->where([
                'users.is_active' => User::ACTIVE,
                'users.is_deleted' => User::NOT_DELETED,
                'post_like.post_id' => $postId,
                'post_like.type' => PostLike::TYPE_COMMENT_LIKE
            ])
            ->count();
    }

    public function getCountByUserIdAndPostId($userId, $postId)
    {
        return $this->model
            ->where([
                'user_id' => $userId,
                'post_id' => $postId
            ])->count();
    }

    public function getCountByUserIdAndPostIdAndType(int $userId, int $postId, int $type)
    {
        return $this->model
            ->where([
                'user_id' => $userId,
                'post_id' => $postId,
                'type' => $type
            ])->count();
    }

    public function getPostLikesCount($post)
    {
        return $this->model
            ->where([
                'post_id' => $post->id
            ])
            ->count();
    }

    public function getByUserIdAndPostId(int $userId, int $postId)
    {
        return $this->model
            ->where([
                'user_id' => $userId,
                'post_id' => $postId
            ])
            ->first();
    }

    public function getByUserIdAndPostIdAndType(int $userId, int $postId, int $type)
    {
        return $this->model
            ->where([
                'user_id' => $userId,
                'post_id' => $postId,
                'type' => $type
            ])
            ->first();
    }
}
