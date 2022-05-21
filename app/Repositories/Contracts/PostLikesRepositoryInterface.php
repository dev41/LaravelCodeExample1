<?php

namespace App\Repositories\Contracts;

interface PostLikesRepositoryInterface
{
    public function getByKey($value);

    public function store($data);

    public function all();

    public function destroyAllByPostId($postId);

    public function getUserCommentLikesCount(int $postId);

    public function getCountByUserIdAndPostId($userId, $postId);

    public function getCountByUserIdAndPostIdAndType(int $userId, int $postId, int $type);

    public function getPostLikesCount($post);

    public function getByUserIdAndPostId(int $userId, int $postId);

    public function getByUserIdAndPostIdAndType(int $userId, int $postId, int $type);
}