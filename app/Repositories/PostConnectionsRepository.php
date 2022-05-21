<?php

namespace App\Repositories;

use App\Models\EmailTemplate;
use App\Models\Notification;
use App\Models\Post;
use App\Models\PostComment;
use App\Models\PostConnection;
use App\Models\PostLike;
use App\Models\Settings;
use App\Repositories\Contracts\EmailTemplatesRepositoryInterface;
use App\Repositories\Contracts\NotificationsRepositoryInterface;
use App\Repositories\Contracts\PostCommentsRepositoryInterface;
use App\Repositories\Contracts\PostConnectionsRepositoryInterface;
use App\Repositories\Contracts\PostLikesRepositoryInterface;
use App\Repositories\Contracts\PostsRepositoryInterface;
use App\Traits\RepositoryTrait;

class PostConnectionsRepository implements PostConnectionsRepositoryInterface
{
    use RepositoryTrait;
    
    protected $model;

    public function __construct(PostConnection $postConnection)
    {
        $this->model = $postConnection;
    }

    public function destroyAllByPostId($postId)
    {
        return $this->model->where([
            'post_id' => $postId
        ])->delete();
    }
}