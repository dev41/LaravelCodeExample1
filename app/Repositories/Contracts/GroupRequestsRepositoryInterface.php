<?php

namespace App\Repositories\Contracts;

use App\User;

interface GroupRequestsRepositoryInterface
{
    public function getByKey($value);

    public function store($data);

    public function update($data, $conditions);

    public function delete(array $conditions);

    public function getByGroupIdAndUserId($groupId, $userId);

    public function getAllByGroupIdAndUserId($groupId, $userId);

    public function getCountByGroupIdAndUserId($groupId, $userId);

    public function getPendingByGroupIdWithUserInfo($groupId, int $offset = 0, int $limit = 10);

    public function getPendingGroupRequestsCount(int $groupId);

    public function getByGroupId($groupId);

    public function getAllUserPendingIsAdminRequest($userId);

    public function getByGroupIdWithUsersInfo(int $groupId);

    public function getUserPendingGroupRequest(User $user, int $limit = 10);
}