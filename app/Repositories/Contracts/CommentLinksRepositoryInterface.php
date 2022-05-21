<?php

namespace App\Repositories\Contracts;

interface CommentLinksRepositoryInterface
{
    public function getByKey($value);

    public function store($data);

    public function destroyAllByCommentId(int $commentId);

    public function getFirstByCommentId(int $commentId);

    public function all();
}