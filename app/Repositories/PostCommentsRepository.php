<?php

namespace App\Repositories;

use App\Helpers\AzureBlob;
use App\Models\EmailTemplate;
use App\Models\Notification;
use App\Models\Post;
use App\Models\PostComment;
use App\Models\PostLike;
use App\Models\Settings;
use App\Models\UserPrivacySettings;
use App\Repositories\Contracts\CommentLinksRepositoryInterface;
use App\Repositories\Contracts\EmailTemplatesRepositoryInterface;
use App\Repositories\Contracts\FriendsRepositoryInterface;
use App\Repositories\Contracts\NotificationsRepositoryInterface;
use App\Repositories\Contracts\PostCommentsRepositoryInterface;
use App\Repositories\Contracts\PostLikesRepositoryInterface;
use App\Repositories\Contracts\PostsRepositoryInterface;
use App\Repositories\Contracts\UserRepositoryInterface;
use App\Traits\RepositoryTrait;
use App\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class PostCommentsRepository implements PostCommentsRepositoryInterface
{
    use RepositoryTrait;
    
    protected $model;

    public function __construct(PostComment $postComment)
    {
        $this->model = $postComment;
    }

    public function destroyAllByPostId($postId)
    {
        return $this->model->where([
            'post_id' => $postId
        ])->delete();
    }

    public function destroyById($id)
    {
        return $this->model->where([
            'id' => $id
        ])->delete();
    }

    public function getPostCommentsCount($post)
    {
        return $this->model
            ->where([
                'post_id' => $post->id,
                'is_blocked' => PostComment::IS_NOT_BLOCKED
            ])
            ->count();
    }

    public function getPostComments($postId)
    {
        return $this->model
            ->join('users', 'users.id', '=', 'post_comment.user_id')
            ->select([
                'post_comment.*', 'users.first_name', 'users.last_name', 'users.name', 'users.profile_image',
                'users.display_name'
            ])
            ->where([
                'users.is_active' => User::ACTIVE,
                'users.is_deleted' => User::NOT_DELETED,
                'post_comment.post_id' => $postId,
                'post_comment.is_blocked' => PostComment::IS_NOT_BLOCKED
            ])
            ->orderBy('post_comment.id', 'ASC')
            ->get();
    }

    public function getPostCommentsList($post, $ignores, $onlyCount = false, $commentId = null, $page = 0, $pageSize = 5)
    {
        $query = $this->model
            ->join('users', 'users.id', '=', 'post_comment.user_id')
            ->select([
                'post_comment.*'
            ])
            ->where([
                'users.is_active' => User::ACTIVE,
                'users.is_deleted' => User::NOT_DELETED,
                'post_comment.post_id' => $post->id,
                'post_comment.is_blocked' => PostComment::IS_NOT_BLOCKED,
                'post_comment.comment_id' => $commentId
            ])
            ->whereNotIn('post_comment.id', $ignores)
            ->offset($page * $pageSize)
            ->take($pageSize)
            ->orderBy('post_comment.c_date', 'DESC');

        if ($onlyCount) {
            return $query->count();
        }

        return $query->get();
    }

    public function getByIdAndUserId(int $commentId, int $userId)
    {
        return $this->model
            ->where([
                'user_id' => $userId,
                'id' => $commentId
            ])
            ->first();
    }

    public function getNotDeletedByPostId(int $postId)
    {
        return $this->model
            ->where([
                'is_delete' => PostComment::IS_NOT_DELETED,
                'post_id' => $postId
            ])
            ->orderBy('id', 'DESC')
            ->get();
    }
}