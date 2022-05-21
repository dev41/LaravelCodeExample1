<?php

namespace App\Repositories;

use App\Models\CommentLink;
use App\Repositories\Contracts\CommentLinksRepositoryInterface;
use App\Traits\RepositoryTrait;

class CommentLinksRepository implements CommentLinksRepositoryInterface
{
    use RepositoryTrait;
    
    protected $model;

    public function __construct(CommentLink $model)
    {
        $this->model = $model;
    }

    public function destroyAllByCommentId(int $commentId)
    {
        return $this->model->where([
            'comment_id' => $commentId
        ])->delete();
    }

    public function getFirstByCommentId(int $commentId)
    {
        return $this->model->where([
            'comment_id' => $commentId
        ])->first();
    }
}