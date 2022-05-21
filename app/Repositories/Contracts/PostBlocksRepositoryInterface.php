<?php

namespace App\Repositories\Contracts;

interface PostBlocksRepositoryInterface
{
    public function getByKey($value);

    public function store($data);

    public function delete(array $conditions);

    public function getByGroupIdAndGroupType($groupId, $groupType);

    public function getByGroupIdWithUserInfo(int $groupId);

    public function getBlockedPost(int $postId, int $groupId, int $userId, int $groupType);

    public function getBlockedComment(int $postId, int $groupId, int $userId, int $groupType);

    public function getByGroupIdUserIdAndGroupType(int $groupId, int $userId, int $groupType);
}