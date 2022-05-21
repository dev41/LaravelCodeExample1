<?php

namespace App\Repositories\Contracts;

interface PostsRepositoryInterface
{
    public function getByKey($value);

    public function store($data);

    public function getByIdAndUserId($postId, $userId);

    public function getPostsForUser(array $conditions);

    public function getPostsForUserProfile(array $conditions);

    public function getAllGroupPosts(array $conditions);

    public function getByIdAndGroupId(int $postId, int $groupId);

    public function getActiveById(int $postId);
}
