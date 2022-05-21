<?php

namespace App\Repositories\Contracts;

interface PostLinksRepositoryInterface
{
    public function getByKey($value);

    public function destroyAllByPostId($postId);

    public function store($data);

    public function all();
}