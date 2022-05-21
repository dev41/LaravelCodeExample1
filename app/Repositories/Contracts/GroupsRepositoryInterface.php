<?php

namespace App\Repositories\Contracts;

use App\Models\Group;
use App\User;

interface GroupsRepositoryInterface
{
    public function getByKey($value);

    public function store($data);

    public function update($data, $conditions);

    public function all();

    public function getByGroupRequestId($groupRequestId);

    public function getQueryByKeyword($keyword);

    public function getByIdAndType($groupId, $type);

    public function getActiveGroupById($groupId);

    public function getActiveGroupsByUserId($userId);

    public function getGroupDetailBySlug($groupSlug);

    public function getGroupsInfoWhereUserExists($groupsIds);

    public function getAllActiveGroupsWithUserInfo();

    public function getByIdWithUserInfo($groupId);

    public function getByCategoryIdWithUserDetails($categoryId);

    public function getCountByCategoryId(int $categoryId, int $active);

    public function getActiveGroups(int $limit = 10);

    public function searchByKeyword(string $keyword, $limit = 10);

    public function getBySlug(string $slug);

    public function getAllActiveCount();

    public function getActiveByCategoryId(int $categoryId, int $limit = 10);

    public function getUserGroupsList(int $userId, int $limit = 10);

    public function getUserGroupsListCount(int $userId);

    public function deleteAdmin(Group $group, User $admin);
}