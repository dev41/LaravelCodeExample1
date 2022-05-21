<?php

namespace App\Repositories\Contracts;

use App\User;

interface NotificationsRepositoryInterface
{
    public function getByKey($value);

    public function delete(array $conditions);

    public function destroyByTypeAndTablePIds($type, $tablePId);

    public function getUserNotifications($user);

    public function getNotViewedUserNotifications($userId);

    public function setViewedByIds($notificationsIds);

    public function setUserNotificationsAsViewed($user);

    public function destroyByTableIdAndTableName(array $tableIds, string $tableName);

    public function deleteByType(array $types, int $tablePId);

    public function getByUser(User $user, int $limit = 10);

    public function getCountByUserAndReadStatus(User $user, int $isRead);

    public function getByUserAndReadStatus(User $user, int $status, int $limit = 10);

    public function deleteAllUserNotifications(User $user);

    public function deleteGroupNotifications(int $groupId);

    public function deleteUserFriendNotifications(int $userId, int $friendId);
}