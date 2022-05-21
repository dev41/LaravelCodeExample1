<?php

namespace App\Repositories;

use App\Models\Group;
use App\Models\GroupConnection;
use App\Repositories\Contracts\GroupConnectionsRepositoryInterface;
use App\Traits\RepositoryTrait;
use App\User;

class GroupConnectionsRepository implements GroupConnectionsRepositoryInterface
{
    use RepositoryTrait;
    
    protected $model;

    public function __construct(GroupConnection $groupConnection)
    {
        $this->model = $groupConnection;
    }

    public function getByUserId($userId)
    {
        return $this->model->where([
            'user_id' => $userId
        ])->get();
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

    public function getByGroupIdAndUserId($groupId, $userId)
    {
        return $this->model
            ->where([
                'group_id' => $groupId,
                'user_id' => $userId
            ])
            ->first();
    }

    public function getGroupMembers($groupId, int $offset = 0, int $limit = 10)
    {
        return $this->model
            ->join('users', 'users.id', '=', 'group_connections.user_id')
            ->select([
                'group_connections.*', 'users.first_name', 'users.last_name', 'users.name', 'users.profile_image',
                'users.display_name', 'users.bio'
            ])
            ->where([
                'users.is_active' => User::ACTIVE,
                'users.is_deleted' => User::NOT_DELETED,
                'group_connections.group_id' => $groupId
            ])
            ->orderBy('group_connections.id', 'DESC')
            ->groupBy('users.id')
            ->distinct()
            ->skip($offset)
            ->take($limit)
            ->get();
    }

    public function getGroupMembersCount(int $groupId)
    {
        return $this->model
            ->join('users', 'users.id', '=', 'group_connections.user_id')
            ->select([
                'group_connections.*', 'users.first_name', 'users.last_name', 'users.name', 'users.profile_image',
                'users.display_name', 'users.bio'
            ])
            ->where([
                'users.is_active' => User::ACTIVE,
                'users.is_deleted' => User::NOT_DELETED,
                'group_connections.group_id' => $groupId
            ])
            ->count();
    }

    public function getCountByGroupId(Group $group)
    {
        return $this->model
            ->where([
                'group_id' => $group->id
            ])
            ->count();
    }

    public function getGroupMembersCountByGroupId($groupId)
    {
        return $this->model
            ->leftJoin('users', 'group_connections.user_id', '=', 'users.id')
            ->where([
                'users.is_active' => User::ACTIVE,
                'users.is_deleted' => User::NOT_DELETED,
                'group_connections.group_id' => $groupId
            ])
            ->count();
    }

    public function getByGroupId($groupId)
    {
        return $this->model
            ->where('group_id', $groupId)
            ->get();
    }

    public function getFirstByGroupId($groupId)
    {
        return $this->model
            ->where('group_id', $groupId)
            ->first();
    }

    public function getUserGroupsList($userId, int $offset = 0, int $limit = 10)
    {
        return $this->model
            ->join('groups', 'group_connections.group_id', '=', 'groups.id')
            ->join('users', 'users.id', '=', 'groups.user_id')
            ->select([
                'groups.*', 'group_connections.user_id AS conuser_id', 'group_connections.group_id',
                'users.first_name', 'users.last_name', 'users.name', 'users.profile_image', 'users.display_name'
            ])
            ->where([
                'group_connections.user_id' => $userId,
                'groups.status' => Group::STATUS_ACTIVE
            ])
            ->orderBy('group_connections.id', 'DESC')
            ->offset($offset)
            ->take($limit)
            ->get();
    }

    public function getUserGroupsCount(int $userId)
    {
        return $this->model
            ->join('groups', 'group_connections.group_id', '=', 'groups.id')
            ->join('users', 'users.id', '=', 'groups.user_id')
            ->select([
                'groups.*', 'group_connections.user_id AS conuser_id', 'group_connections.group_id',
                'users.first_name', 'users.last_name', 'users.name', 'users.profile_image', 'users.display_name'
            ])
            ->where([
                'group_connections.user_id' => $userId,
                'groups.status' => Group::STATUS_ACTIVE
            ])
            ->orderBy('group_connections.id', 'DESC')
            ->count();
    }
}