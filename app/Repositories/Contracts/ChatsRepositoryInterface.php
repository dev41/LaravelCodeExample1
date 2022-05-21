<?php

namespace App\Repositories\Contracts;

use App\Models\Chat;
use App\User;

interface ChatsRepositoryInterface
{
    public function getByKey($value);

    public function store($data);

    public function getByUsersIds(int $firstUserId, int $secondUserId);

    public function getAllUserActiveChats(User $user, array $conditions);

    public function getUserNotViewedMessages(Chat $chat, User $user);

    public function setUserMessagesAsRead(Chat $chat, User $user);

    public function getUserUnreadChatsCount(int $userId);

    public function getAllUserChats(User $user);

    public function getChatMessages(Chat $chat, array $conditions);
}