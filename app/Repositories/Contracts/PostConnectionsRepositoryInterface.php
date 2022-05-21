<?php

namespace App\Repositories\Contracts;

interface PostConnectionsRepositoryInterface
{
    public function getByKey($value);

    public function destroyAllByPostId($postId);
}