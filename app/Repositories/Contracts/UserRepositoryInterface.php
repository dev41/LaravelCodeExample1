<?php

namespace App\Repositories\Contracts;

use App\Models\Group;
use App\User;
use App\Models\Hub;

interface UserRepositoryInterface
{
    public function getByKey($value);

    public function store($data);

    public function update($data, $conditions);

    public function all();

    public function getByEmailAndType($email, $type);

    public function getByEmailAndPassword($email, $password);

    public function getByEmail($email);

    public function getByResetPasswordToken($token);

    public function getQueryByKeyword($keyword);

    public function getUsersForInvitation($userId);

    public function searchUniqueUserFriendsByName($name, $ignoredFriends);

    public function getByDisplayName($displayName);

    public function getPublicGroupMembersList($ignoredMembers);

    public function getLikedPostUsersList($postId, int $type = 1);

    public function getAllSiteUsers(int $userId, int $companyId);

    public function getUsersByHubId(int $hubId);

    public function getUsersByIds(int $userId, array $ignoredIds);

    public function getPrivateHubPendingUsers(Hub $hub, int $limit = 10);

    public function getUserForHubInvitation(Hub $hub, array $conditions, int $limit = 10);

    public function getHubAttenders(Hub $hub, int $limit = 10);

    public function getUserForGroupInvitation(Group $group, array $conditions, int $limit = 10);

    public function getUserFriendsForGroupInvitation(Group $group, User $user, int $limit = 10);

    public function getUserFriendsForHubInvitation(Hub $hub, User $user, int $limit = 10);
}