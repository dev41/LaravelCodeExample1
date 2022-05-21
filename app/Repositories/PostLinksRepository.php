<?php

namespace App\Repositories;

use App\Models\PostLink;
use App\Repositories\Contracts\PostLinksRepositoryInterface;
use App\Traits\RepositoryTrait;

class PostLinksRepository implements PostLinksRepositoryInterface
{
    use RepositoryTrait;
    
    protected $model;

    public function __construct(PostLink $postLink)
    {
        $this->model = $postLink;
    }

    public function destroyAllByPostId($postId)
    {
        return $this->model->where([
            'post_id' => $postId
        ])->delete();
    }
}
