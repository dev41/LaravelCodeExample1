<?php

namespace App\Repositories\Contracts;

use App\User;

interface FriendsRepositoryInterface
{
    public function getByKey($value);

    public function store($data);

    public function update($data, $conditions);

    public function getByUserIdAndFriendId($userId, $friendId);

    public function getPendingByUserIdAndFriendId($userId, $friendId);

    public function getUserFriends($userId);

    public function getBlockedUserFriends($user);

    public function getUserFriendsList($userId);

    public function getUserFriendRequestsCount($userId);

    public function getUserPendingFriendsList($userId);

    public function setFriendRequestsAsRead($friendsIds);

    public function isUsersAreFriends($firstUserId, $secondUserId);

    public function isFriendExists($userId, $friendId);

    public function getAllUserFriends($userId);

    public function getAllBlockedUserFriends($userId);

    public function getUserFriendsExcludingIgnored($userId, $ignoredMembers);

    public function setIsReadByIds($friendIds);

    public function getByUserIdFriendIdAndRequestType(int $userId, int $friendId, int $requestType);

    public function getByUsersIds(int $firstUserId, int $secondUserId);

    public function getAllByUserIdExcludingIgnored(int $userId, array $ignoredIds = []);

    public function getAllUserFriendsList(User $user);

    public function searchFriendsByName(User $user, string $searchPhrase);

    public function deleteFriend(int $userId, int $friendId);

    public function deleteAllUserFriends(User $user);

    public function getOutgoingFriendRequests(User $user, int $limit);
}