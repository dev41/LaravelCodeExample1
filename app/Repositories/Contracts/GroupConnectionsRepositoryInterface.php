<?php

namespace App\Repositories\Contracts;

use App\Models\Group;

interface GroupConnectionsRepositoryInterface
{
    public function getByKey($value);

    public function store($data);

    public function delete(array $conditions);

    public function getByUserId($userId);

    public function getCountByGroupIdAndUserId($groupId, $userId);

    public function getByGroupIdAndUserId($groupId, $userId);

    public function getGroupMembers($groupId, int $offset = 0, int $limit = 10);

    public function getCountByGroupId(Group $group);

    public function getGroupMembersCountByGroupId($groupId);

    public function getByGroupId($groupId);

    public function getFirstByGroupId($groupId);

    public function getUserGroupsList($userId, int $offset = 0, int $limit = 10);

    public function getUserGroupsCount(int $userId);

    public function getGroupMembersCount(int $groupId);
}