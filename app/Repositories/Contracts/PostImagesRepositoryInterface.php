<?php

namespace App\Repositories\Contracts;

interface PostImagesRepositoryInterface
{
    public function getByKey($value);

    public function getAllByPostId($postId);

    public function store($data);

    public function getAllByPostIdAndVersion(int $postId, int $version);
}