<?php

namespace App\Repositories;

use App\Models\EmailTemplate;
use App\Models\Notification;
use App\Models\Post;
use App\Models\PostComment;
use App\Models\PostImage;
use App\Models\PostLike;
use App\Models\Settings;
use App\Repositories\Contracts\EmailTemplatesRepositoryInterface;
use App\Repositories\Contracts\NotificationsRepositoryInterface;
use App\Repositories\Contracts\PostCommentsRepositoryInterface;
use App\Repositories\Contracts\PostConnectionsRepositoryInterface;
use App\Repositories\Contracts\PostImagesRepositoryInterface;
use App\Repositories\Contracts\PostLikesRepositoryInterface;
use App\Repositories\Contracts\PostsRepositoryInterface;
use App\Traits\RepositoryTrait;

class PostImagesRepository implements PostImagesRepositoryInterface
{
    use RepositoryTrait;
    
    protected $model;

    public function __construct(PostImage $postImage)
    {
        $this->model = $postImage;
    }

    public function getAllByPostId($postId)
    {
        return $this->model->where([
            'post_id' => $postId
        ])->get();
    }

    public function getAllByPostIdAndVersion(int $postId, int $version)
    {
        return $this->model->where([
            'post_id' => $postId,
            'v2' => $version
        ])->get();
    }
}