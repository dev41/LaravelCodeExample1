<?php

namespace App\Repositories;

use App\Models\Group;
use App\Models\GroupRequest;
use App\Repositories\Contracts\GroupRequestsRepositoryInterface;
use App\Traits\RepositoryTrait;
use App\User;

class GroupRequestsRepository implements GroupRequestsRepositoryInterface
{
    use RepositoryTrait;
    
    protected $model;

    public function __construct(GroupRequest $model)
    {
        $this->model = $model;
    }

    public function getByGroupIdAndUserId($groupId, $userId)
    {
        return $this->model
            ->where([
                'group_id' => $groupId,
                'user_id' => $userId
            ])
            ->first();
    }

    public function getAllByGroupIdAndUserId($groupId, $userId)
    {
        return $this->model
            ->where([
                'group_id' => $groupId,
                'user_id' => $userId
            ])
            ->get();
    }

    public function getCountByGroupIdAndUserId($groupId, $userId)
    {
        return $this->model
            ->where([
                'group_id' => $groupId,
                'user_id' => $userId
            ])
            ->count();
    }

    public function getPendingByGroupIdWithUserInfo($groupId, int $offset = 0, int $limit = 10)
    {
        return $this->model
            ->join('users', 'users.id', '=', 'group_requests.user_id')
            ->select([
                'group_requests.*', 'users.first_name', 'users.email', 'users.last_name', 'users.name',
                'users.profile_image', 'users.display_name'
            ])
            ->where([
                'group_requests.group_id' => $groupId,
                'group_requests.request_type' => GroupRequest::TYPE_REQUEST_PENDING
            ])
            ->whereNull('invited_by')
            ->orderByDesc('group_requests.id')
            ->offset($offset)
            ->take($limit)
            ->get();
    }

    public function getPendingGroupRequestsCount(int $groupId)
    {
        return $this->model
            ->where([
                'group_requests.group_id' => $groupId,
                'group_requests.request_type' => GroupRequest::TYPE_REQUEST_PENDING
            ])
            ->count();
    }

    public function getByGroupId($groupId)
    {
        return $this->model
            ->where('group_id', $groupId)
            ->get();
    }

    public function getAllUserPendingIsAdminRequest($userId)
    {
        return $this->model
            ->join('groups', 'groups.id', '=', 'group_requests.group_id')
            ->select([
                'group_requests.*', 'groups.slug', 'groups.group_uname', 'groups.group_name', 'groups.group_type',
                'groups.short_desc', 'groups.image', 'groups.long_desc'
            ])
            ->where([
                'group_requests.user_id' => $userId,
                'group_requests.is_admin_request' => GroupRequest::IS_ADMIN_ANSWER
            ])
            ->get();
    }

    public function getByGroupIdWithUsersInfo(int $groupId)
    {
        return $this->model
            ->join('users', 'users.id', '=', 'group_requests.user_id')
            ->select([
                'group_requests.*', 'users.first_name', 'users.last_name', 'users.name', 'users.profile_image',
                'users.display_name', 'users.bio'
            ])
            ->where([
                'group_requests.group_id' => $groupId
            ])
            ->orderBy('group_requests.id', 'DESC')
            ->get();
    }

    public function getUserPendingGroupRequest(User $user, int $limit = 10)
    {
        return $this->model
            ->select(['group_requests.*'])
            ->join('groups', 'groups.id', '=', 'group_requests.group_id')
            ->where([
                'group_requests.user_id' => $user->id,
                'groups.status' => Group::STATUS_ACTIVE
            ])
            ->whereIn('group_requests.request_type', [0, GroupRequest::TYPE_REQUEST_PENDING])
            ->whereNotNull('group_requests.invited_by')
            ->paginate($limit);
    }
}