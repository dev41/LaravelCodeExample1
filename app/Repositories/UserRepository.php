<?php

namespace App\Repositories;

use App\Models\Hub;
use App\Models\HubInvite;
use App\Models\Friend;
use App\Models\Group;
use App\Models\UserPrivacySettings;
use App\Repositories\Contracts\CompanySubAdminsRepositoryInterface;
use App\Repositories\Contracts\UserRepositoryInterface;
use App\Traits\RepositoryTrait;
use App\User;
use Carbon\Carbon;

class UserRepository implements UserRepositoryInterface
{
    use RepositoryTrait;
    
    protected $model;

    public function __construct(User $user)
    {
        $this->model = $user;
    }

    public function getByEmailAndType($email, $type)
    {
        return $this->model->where([
            'email' => $email,
            'user_type' => $type
        ])->first();
    }

    public function getByEmailAndPassword($email, $password)
    {
        return $this->model->where([
            'email' => $email,
            'app_password' => md5($password)
        ])->first();
    }

    public function getByEmail($email)
    {
        return $this->model->where([
            'email' => $email
        ])->first();
    }

    public function getByResetPasswordToken($token)
    {
        return $this->model
            ->join('password_resets', 'users.id', '=', 'password_resets.user_id')
            ->select('users.*')
            ->where([
                'password_resets.token' => $token
            ])
            ->where('password_resets.expire_to', '>=', Carbon::now())
            ->first();
    }

    public function getQueryByKeyword($keyword)
    {
        $query = $this->model
            ->select(['users.*'])
            ->leftJoin('user_privacy_settings', 'users.id', '=', 'user_privacy_settings.user_id')
            ->where([
                'users.is_deleted' => User::NOT_DELETED,
                'users.is_active' => User::ACTIVE,
                'users.user_type' => User::TYPE_CUSTOMER
            ]);

        if ($keyword != "") {
            $query->where(function ($query) use ($keyword) {
                $query->where(function ($query) use ($keyword) {
                    $query->where(function ($query) use ($keyword) {
                        $query->where(function ($query) use ($keyword) {
                            $query->where('name', 'like', '%' . addslashes($keyword) . '%')
                                ->where('user_privacy_settings.name_visible', UserPrivacySettings::FULL_NAME_VISIBLE);
                        })
                        ->orWhere(function ($query) use ($keyword) {
                            $query->where('first_name', 'like', '%' . addslashes($keyword) . '%')
                                ->where('user_privacy_settings.name_visible', UserPrivacySettings::FIRST_NAME_ONLY_VISIBLE);
                        });
                    })->where(['user_privacy_settings.name_visible_in_search_engine' => UserPrivacySettings::NAME_VISIBLE_IN_SEARCH_ENGINE]);
                })->orWhere(function ($query) use ($keyword) {
                    $query->where('email', 'like', '%' . addslashes($keyword) . '%')
                        ->where(['user_privacy_settings.found_email_address' => UserPrivacySettings::FIND_BY_EMAIL]);
                })->orWhere(function ($query) use ($keyword) {
                    $query->where('mobile_number', 'like', '%' . addslashes($keyword) . '%')
                        ->where(['user_privacy_settings.found_phone_number' => UserPrivacySettings::FIND_BY_PHONE]);
                });
            });
        }

        $query->groupBy('users.id');

        return $query;
    }

    public function getUsersForInvitation($userId)
    {
        return $this->model
            ->select([
                'id', 'first_name', 'last_name', 'name', 'profile_image', 'display_name'
            ])
            ->where([
                'user_type' => User::TYPE_CUSTOMER,
                'is_deleted' => User::NOT_DELETED,
                'is_active' => User::ACTIVE
            ])
            ->where('id', '<>', $userId)
            ->orderBy('id', 'DESC')
            ->get();
    }

    public function searchUniqueUserFriendsByName($name, $ignoredFriends)
    {
        return $this->model
            ->where(function ($query) use ($name) {
                $query->where('first_name', 'like', "%$name%")
                    ->orWhere('last_name', 'like', "%$name%")
                    ->orWhere('name', 'like', "%$name%");
            })
            ->whereNotIn('id', $ignoredFriends)
            ->orderBy('id', 'DESC')
            ->get();
    }

    public function getByDisplayName($displayName)
    {
        return $this->model
            ->where([
                'display_name' => $displayName
            ])
            ->first();
    }

    public function getPublicGroupMembersList($ignoredMembers)
    {
        return $this->model
            ->select([
                'first_name', 'last_name', 'name', 'profile_image', 'id AS friend_id', 'display_name'
            ])
            ->whereNotIn('id', $ignoredMembers)
            ->where([
                'is_admin' => User::IS_NOT_ADMIN,
                'is_active' => User::ACTIVE,
                'is_deleted' => User::NOT_DELETED,
                'user_type' => User::TYPE_CUSTOMER
            ])
            ->get();
    }

    public function getLikedPostUsersList($postId, int $type = 1)
    {
        return $this->model
            ->join('post_like', 'post_like.user_id', '=', 'users.id')
            ->select([
                'post_like.*', 'users.first_name', 'users.last_name', 'users.name', 'users.email',
                'users.profile_image', 'users.display_name', 'users.bio', 'users.id', 'users.gender'
            ])
            ->where([
                'users.is_deleted' => User::NOT_DELETED,
                'users.is_active' => User::ACTIVE,
                'post_like.post_id' => $postId,
                'post_like.type' => $type
            ])
            ->get();
    }

    public function getAllSiteUsers(int $userId, int $companyId)
    {
        return $this->model
            ->select([
                'first_name', 'last_name', 'name', 'id', 'display_name'
            ])
            ->where([
                'is_active'=> User::ACTIVE,
                'is_deleted' => User::NOT_DELETED
            ])
            ->where('id', '<>', $userId)
            ->where(function ($query) {
                $query->where('user_type', '<>', User::TYPE_SUPER_ADMIN)
                    ->orWhere('user_type', '<>', User::TYPE_CA);
            })
            ->orderBy('id', 'DESC')
            ->get();
    }

    public function getUsersByHubId(int $hubId)
    {
        return $this->model
            ->join('hub_invites', 'users.id', '=', 'hub_invites.user_id')
            ->select([
                'users.id', 'users.first_name as text'
            ])
            ->where([
                'users.is_active' => User::ACTIVE,
                'users.is_deleted' => User::NOT_DELETED,
                'hub_invites.hub_id' => $hubId
            ])
            ->get();
    }

    public function getUsersByIds(int $userId, array $ignoredIds)
    {
        return $this->model
            ->select([
                'users.id', 'users.first_name', 'users.last_name', 'users.name', 'users.profile_image',
                'users.display_name'
            ])
            ->whereIn('id', $ignoredIds)
            ->where('id', '<>', $userId)
            ->get();
    }

    public function getPrivateHubPendingUsers(Hub $hub, int $limit = 10)
    {
        return $this->model
            ->select(['users.*'])
            ->join('hub_invites', 'users.id', '=', 'hub_invites.user_id')
            ->where([
                'hub_invites.hub_id' => $hub->id,
                'hub_invites.status' => HubInvite::STATUS_PENDING,
                'hub_invites.is_request' => HubInvite::IS_REQUEST
            ])
            ->paginate($limit);
    }

    public function getUserForHubInvitation(Hub $hub, array $conditions, int $limit = 10)
    {
        $query = $this->model
            ->select(['users.*'])
            ->leftJoin('user_privacy_settings', 'users.id', '=', 'user_privacy_settings.user_id')
            ->whereNotNull('app_password')
            ->whereRaw("(SELECT COUNT(*) FROM hub_invites WHERE hub_invites.user_id=users.id AND hub_invites.hub_id={$hub->id})<=0");

        if (isset($conditions['search'])) {
            $query->where(function ($query) use ($conditions) {
                $query->where(function ($query) use ($conditions) {
                    $query->where('name', 'like', "%{$conditions['search']}%")
                        ->where('user_privacy_settings.name_visible', UserPrivacySettings::FULL_NAME_VISIBLE);
                })
                    ->orWhere(function ($query) use ($conditions) {
                        $query->where('first_name', 'like', "%{$conditions['search']}%")
                            ->where('user_privacy_settings.name_visible', UserPrivacySettings::FIRST_NAME_ONLY_VISIBLE);
                    });
            })->where(['user_privacy_settings.name_visible_in_search_engine' => UserPrivacySettings::NAME_VISIBLE_IN_SEARCH_ENGINE]);
        }

        return $query
            ->groupBy('users.id')
            ->distinct()
            ->paginate($limit);
    }

    public function getHubAttenders(Hub $hub, int $limit = 10)
    {
        return $this->model
            ->select(['users.*'])
            ->join('hub_invites', 'users.id', '=', 'hub_invites.user_id')
            ->where([
                'hub_invites.status' => HubInvite::STATUS_ACCEPTED,
                'hub_invites.hub_id' => $hub->id
            ])
            ->paginate($limit);
    }

    public function getUserForGroupInvitation(Group $group, array $conditions, int $limit = 10)
    {
        $query = $this->model
            ->select(['users.*'])
            ->leftJoin('user_privacy_settings', 'users.id', '=', 'user_privacy_settings.user_id')
            ->whereNotNull('app_password')
            ->whereRaw("
                (SELECT COUNT(group_connections.id) FROM group_connections WHERE group_connections.user_id=users.id AND group_connections.group_id={$group->id})<=0
                AND (SELECT COUNT(group_requests.id) FROM group_requests WHERE group_requests.user_id=users.id AND group_requests.group_id={$group->id})<=0
            ");
        if (isset($conditions['search'])) {
            $query->where(function ($query) use ($conditions) {
                $query->where(function ($query) use ($conditions) {
                    $query->where('name', 'like', "%{$conditions['search']}%")
                        ->where('user_privacy_settings.name_visible', UserPrivacySettings::FULL_NAME_VISIBLE);
                })
                    ->orWhere(function ($query) use ($conditions) {
                        $query->where('first_name', 'like', "%{$conditions['search']}%")
                            ->where('user_privacy_settings.name_visible', UserPrivacySettings::FIRST_NAME_ONLY_VISIBLE);
                    });
            })->where(['user_privacy_settings.name_visible_in_search_engine' => UserPrivacySettings::NAME_VISIBLE_IN_SEARCH_ENGINE]);
        }
        return $query
            ->groupBy('users.id')
            ->distinct()
            ->paginate($limit);
    }

    public function getUserFriendsForGroupInvitation(Group $group, User $user, int $limit = 10)
    {
        return $this->model
            ->select(['users.*'])
            ->join('friends', function ($join) {
                $join->on('users.id', '=', 'friends.user_id')
                    ->orOn('users.id', '=', 'friends.friend_id');
            })
            ->where(function ($query) use ($user) {
                $query->where([
                    'friends.user_id' => $user->id
                ])
                    ->orWhere([
                        'friends.friend_id' => $user->id
                    ]);
            })
            ->where([
                'friends.request_type' => Friend::REQUEST_TYPE_ACCEPT_FRIEND
            ])
            ->whereRaw("
                (SELECT COUNT(group_connections.id) FROM group_connections WHERE group_connections.user_id=users.id AND group_connections.group_id={$group->id})<=0
                AND (SELECT COUNT(group_requests.id) FROM group_requests WHERE group_requests.user_id=users.id AND group_requests.group_id={$group->id})<=0
            ")
            ->where('users.id', '<>', $user->id)
            ->groupBy('users.id')
            ->distinct()
            ->paginate($limit);
    }

    public function getUserFriendsForHubInvitation(Hub $hub, User $user, int $limit = 10)
    {
        return $this->model
            ->select(['users.*'])
            ->join('friends', function ($join) {
                $join->on('users.id', '=', 'friends.user_id')
                    ->orOn('users.id', '=', 'friends.friend_id');
            })
            ->where(function ($query) use ($user) {
                $query->where([
                    'friends.user_id' => $user->id
                ])
                    ->orWhere([
                        'friends.friend_id' => $user->id
                    ]);
            })
            ->where([
                'friends.request_type' => Friend::REQUEST_TYPE_ACCEPT_FRIEND
            ])
            ->whereRaw("
                (SELECT COUNT(hub_invites.id) FROM hub_invites WHERE hub_invites.user_id=users.id AND hub_invites.hub_id={$hub->id})<=0
            ")
            ->where('users.id', '<>', $user->id)
            ->groupBy('users.id')
            ->distinct()
            ->paginate($limit);
    }
}