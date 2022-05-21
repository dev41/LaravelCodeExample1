<?php

namespace App\Repositories;

use App\Models\Friend;
use App\Models\Group;
use App\Models\UserPrivacySettings;
use App\Repositories\Contracts\FriendsRepositoryInterface;
use App\Traits\RepositoryTrait;
use App\User;

class FriendsRepository implements FriendsRepositoryInterface
{
    use RepositoryTrait;
    
    protected $model;

    /**
     * FriendsRepository constructor.
     * @param Friend $friend
     */
    public function __construct(Friend $friend)
    {
        $this->model = $friend;
    }

    public function getByUserIdAndFriendId($userId, $friendId)
    {
        return $this->model
            ->where(function ($query) use ($userId, $friendId) {
                $query->where([
                    'user_id' => $userId,
                    'friend_id' => $friendId
                ])
                ->orWhere(function ($query) use ($userId, $friendId) {
                    $query->where(['user_id' => $friendId])
                        ->where(['friend_id' => $userId]);
                });
            })
            ->where([
                'request_type' => Friend::REQUEST_TYPE_ACCEPT_FRIEND
            ])
            ->unBlocked()
            ->first();
    }

    public function getPendingByUserIdAndFriendId($userId, $friendId)
    {
        return $this->model
            ->where(function ($query) use ($userId, $friendId) {
                $query->where([
                    'user_id' => $userId,
                    'friend_id' => $friendId
                ])
                ->orWhere(function ($query) use ($userId, $friendId) {
                    $query->where(['user_id' => $friendId])
                        ->where(['friend_id' => $userId]);
                });
            })
            ->where([
                'request_type' => Friend::REQUEST_TYPE_REQUEST_FRIEND
            ])
            ->unBlocked()
            ->first();
    }

    public function getUserFriends($userId)
    {
        return $this->model
            ->where('request_type', Friend::REQUEST_TYPE_ACCEPT_FRIEND)
            ->where(function ($query) use ($userId) {
                $query->where([
                    'user_id' => $userId
                ])
                ->orWhere([
                    'friend_id' => $userId
                ]);
            })
            ->unBlocked()
            ->groupBy('user_id')
            ->groupBy('friend_id')
            ->distinct()
            ->get();
    }

    public function getBlockedUserFriends($user)
    {
        return $this->model
            ->select('friend_id')
            ->where([
                'user_id' => $user->id,
                'request_type' => Friend::REQUEST_TYPE_ACCEPT_FRIEND
            ])
            ->unBlocked()
            ->get();
    }

    public function getUserFriendsList($userId)
    {
        return $this->model
            ->join('users', function ($join) {
                $join->on('friends.friend_id', '=', 'users.id')
                    ->orOn('friends.user_id', '=', 'users.id');
            })
            ->select([
                'friends.*', 'users.first_name', 'users.last_name', 'users.name', 'users.email', 'users.profile_image',
                'users.display_name', 'users.bio', 'users.id AS uid', 'users.is_deleted'
            ])
            ->where([
                'users.is_active' => User::ACTIVE,
                'users.is_deleted' => User::NOT_DELETED,
                'friends.request_type' => Friend::REQUEST_TYPE_ACCEPT_FRIEND
            ])
            ->where(function ($query) use ($userId) {
                $query->where([
                    'user_id' => $userId,
                ])
                ->orWhere([
                    'friend_id' => $userId
                ]);
            })
            ->where('users.id', '<>', $userId)
            ->groupBy('users.id')
            ->distinct()
            ->orderBy('friends.id', 'DESC')
            ->get();
    }

    public function getUserFriendRequestsCount($userId)
    {
        return $this->model
            ->where([
                'friend_id' => $userId,
                'request_type' => Friend::REQUEST_TYPE_REQUEST_FRIEND,
                'is_read' => Friend::UNREAD
            ])
            ->count();
    }

    public function getUserPendingFriendsList($userId)
    {
        return $this->model
            ->join('users', 'friends.user_id', '=', 'users.id')
            ->select([
                'friends.*', 'users.first_name', 'users.last_name', 'users.name', 'users.profile_image',
                'users.display_name', 'users.bio', 'users.id AS uid'
            ])
            ->where([
                'friends.friend_id' => $userId,
                'friends.request_type' => Friend::REQUEST_TYPE_REQUEST_FRIEND
            ])
            ->orderBy('users.name')
            ->get();
    }

    public function setFriendRequestsAsRead($friendsIds)
    {
        return $this->model
            ->whereIn('id', $friendsIds)
            ->update([
                'is_read' => Friend::READ
            ]);
    }

    public function isUsersAreFriends($firstUserId, $secondUserId)
    {
        return $this->model
            ->select(['request_type'])
            ->where(function ($query) use ($firstUserId, $secondUserId) {
                $query->where([
                    'user_id' => $firstUserId,
                    'friend_id' => $secondUserId
                ])
                ->orWhere(function ($query) use ($firstUserId, $secondUserId) {
                    $query->where(['user_id' => $secondUserId])
                        ->where(['friend_id' => $firstUserId]);
                });
            })
            ->first();
    }

    public function isFriendExists($userId, $friendId)
    {
        return $this->model
            ->where([
                'user_id' => $userId,
                'friend_id' => $friendId
            ])
            ->orWhere(function ($query) use ($userId, $friendId) {
                $query->where(['user_id' => $friendId])
                    ->where(['friend_id' => $userId]);
            })
            ->first();
    }

    public function getAllUserFriends($userId)
    {
        return $this->model
            ->where([
                'user_id' => $userId
            ])
            ->get();
    }

    public function getAllBlockedUserFriends($userId)
    {
        return $this->model
            ->select('friend_id')
            ->where([
                'user_id' => $userId,
                'request_type' => Friend::REQUEST_TYPE_ACCEPT_FRIEND
            ])
            ->blocked()
            ->get();
    }

    public function getUserFriendsExcludingIgnored($userId, $ignoredMembers)
    {
        return $this->model
            ->join('users', 'users.id', '=', 'friends.friend_id')
            ->select([
                'friends.*', 'users.first_name', 'users.last_name', 'users.name', 'users.profile_image',
                'users.display_name', 'users.id AS uid'
            ])
            ->whereNotIn('friends.friend_id', $ignoredMembers)
            ->where([
                'friends.request_type' => Friend::REQUEST_TYPE_ACCEPT_FRIEND,
                'friends.user_id' => $userId
            ])
            ->get();
    }

    public function setIsReadByIds($friendIds)
    {
        return $this->model
            ->whereIn('id', $friendIds)
            ->update([
                'is_read' => Friend::READ
            ]);
    }

    public function getByUserIdFriendIdAndRequestType(int $userId, int $friendId, int $requestType)
    {
        return $this->model
            ->where([
                'user_id' => $userId,
                'friend_id' => $friendId,
                'request_type' => $requestType
            ])
            ->first();
    }

    public function getByUsersIds(int $firstUserId, int $secondUserId)
    {
        return $this->model
            ->where([
                'user_id' => $firstUserId,
                'friend_id' => $secondUserId
            ])
            ->first();
    }

    public function getAllByUserIdExcludingIgnored(int $userId, array $ignoredIds = [])
    {
        $query = $this->model
            ->where('user_id', $userId);

        if (!empty($ignoredIds)) {
            $query->whereNotIn('friend_id', $ignoredIds);
        }

        return $query->get();
    }

    public function getAllUserFriendsList(User $user)
    {
        return $this->model
            ->where(function ($query) use ($user) {
                $query->where([
                    'user_id' => $user->id
                ])
                ->orWhere(function ($query) use ($user) {
                    $query->where([
                        'friend_id' => $user->id
                    ]);
                });
            })
            ->where('request_type', Friend::REQUEST_TYPE_ACCEPT_FRIEND)
            ->orderByDesc('cdate')
            ->groupBy('user_id')
            ->groupBy('friend_id')
            ->distinct()
            ->get();
    }

    public function searchFriendsByName(User $user, string $searchPhrase)
    {
        return $this->model
            ->select(['friends.*'])
            ->join('users', function ($join) {
                $join->on('friends.friend_id', '=', 'users.id')
                    ->orOn('friends.user_id', '=', 'users.id');
            })
            ->join('user_privacy_settings', 'users.id', '=', 'user_privacy_settings.user_id')
            ->where('request_type', Friend::REQUEST_TYPE_ACCEPT_FRIEND)
            ->where(function ($query) use ($user) {
                $query->where([
                    'friends.user_id' => $user->id
                ])
                ->orWhere([
                    'friends.friend_id' => $user->id
                ]);
            })
            ->where(function ($query) use ($searchPhrase) {
                $query->where(function ($query) use ($searchPhrase) {
                    $query->where('users.name', 'like', "%$searchPhrase%")
                        ->where('user_privacy_settings.name_visible', UserPrivacySettings::FULL_NAME_VISIBLE);
                })
                ->orWhere(function ($query) use ($searchPhrase) {
                    $query->where('users.first_name', 'like', "%$searchPhrase%")
                        ->where('user_privacy_settings.name_visible', UserPrivacySettings::FIRST_NAME_ONLY_VISIBLE);
                });
            })
            ->orderByDesc('cdate')
            ->groupBy('users.id')
            ->distinct()
            ->get();
    }

    public function deleteFriend(int $userId, int $friendId)
    {
        return $this->model
            ->where(function ($query) use ($userId, $friendId) {
                $query->where([
                    'user_id' => $userId,
                    'friend_id' => $friendId
                ])
                ->orWhere(function ($query) use ($userId, $friendId) {
                    $query->where(['user_id' => $friendId])
                        ->where(['friend_id' => $userId]);
                });
            })
            ->delete();
    }

    public function deleteAllUserFriends(User $user)
    {
        return $this->model
            ->where([
                'user_id' => $user->id
            ])
            ->orWhere([
                'friend_id' => $user->id
            ])
            ->delete();
    }

    public function getOutgoingFriendRequests(User $user, int $limit)
    {
        return $this->model
            ->where([
                'user_id' => $user->id,
                'request_type' => Friend::REQUEST_TYPE_REQUEST_FRIEND
            ])
            ->paginate($limit);
    }
}
