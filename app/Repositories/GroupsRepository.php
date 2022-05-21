<?php

namespace App\Repositories;

use App\Models\Group;
use App\Repositories\Contracts\GroupsRepositoryInterface;
use App\Traits\RepositoryTrait;
use App\User;

class GroupsRepository implements GroupsRepositoryInterface
{
    use RepositoryTrait;
    
    protected $model;

    public function __construct(Group $model)
    {
        $this->model = $model;
    }

    public function getByGroupRequestId($groupRequestId)
    {
        return $this->model
            ->leftJoin('group_requests', 'groups.id', '=', 'group_requests.group_id')
            ->where([
                'group_requests.id' => $groupRequestId
            ])
            ->first();
    }

    public function getQueryByKeyword($keyword)
    {
        $query = $this->model
            ->where([
                'status' => Group::STATUS_ACTIVE
            ]);

        if ($keyword != '') {
            $query->where('group_name', 'like', '%' . addslashes($keyword) . '%');
        }

        return $query;
    }

    public function getByIdAndType($groupId, $type)
    {
        return $this->model
            ->where([
                'id' => $groupId,
                'group_type' => $type
            ])
            ->first();
    }

    public function getActiveGroupById($groupId)
    {
        return $this->model
            ->where([
                'id' => $groupId,
                'status' => Group::STATUS_ACTIVE
            ])
            ->first();
    }

    public function getActiveGroupsByUserId($userId)
    {
        return $this->model
            ->where([
                'user_id' => $userId,
                'status' => Group::STATUS_ACTIVE
            ])
            ->orderBy('id', 'DESC')
            ->get();
    }

    public function getGroupDetailBySlug($groupSlug)
    {
        return $this->model
            ->join('users', 'users.id', '=', 'groups.user_id')
            ->join('hub_categories', 'hub_categories.id', '=', 'groups.cat_id')
            ->select([
                'groups.*', 'groups.city AS grp_city', 'hub_categories.title AS grp_cat_name', 'users.first_name',
                'users.last_name', 'users.name', 'users.profile_image', 'users.address AS useraddress', 'users.city',
                'users.state', 'users.display_name', 'users.id AS user_id'
            ])
            ->where([
                'groups.group_uname' => $groupSlug,
                'groups.status' => Group::STATUS_ACTIVE
            ])
            ->first();
    }

    public function getGroupsInfoWhereUserExists($groupsIds)
    {
        return $this->model
            ->join('users', 'users.id', '=', 'groups.user_id')
            ->select([
                'groups.*', 'users.first_name', 'users.last_name', 'users.name', 'users.profile_image',
                'users.display_name'
            ])
            ->whereIn('groups.id', $groupsIds)
            ->where(['groups.status' => Group::STATUS_ACTIVE])
            ->orderBy('groups.id', 'DESC')
            ->get();
    }

    public function getAllActiveGroupsWithUserInfo()
    {
        return $this->model
            ->join('users', 'users.id', '=', 'groups.user_id')
            ->select([
                'groups.*', 'users.first_name', 'users.last_name', 'users.name', 'users.profile_image',
                'users.display_name'
            ])
            ->where([
                'users.is_active' => User::ACTIVE,
                'users.is_deleted' => User::NOT_DELETED,
                'groups.status' => Group::STATUS_ACTIVE
            ])
            ->orderBy('groups.id', 'DESC')
            ->get();
    }

    public function getByIdWithUserInfo($groupId)
    {
        return $this->model
            ->join('users', 'users.id', '=', 'groups.user_id')
            ->select([
                'groups.*', 'users.first_name', 'users.last_name', 'users.name', 'users.profile_image',
                'users.display_name'
            ])
            ->where([
                'groups.id' => $groupId
            ])
            ->first();
    }

    public function getByCategoryIdWithUserDetails($categoryId)
    {
        return $this->model
            ->leftJoin('users', 'groups.user_id', '=', 'users.id')
            ->select([
                'groups.*', 'users.first_name', 'users.last_name', 'users.name', 'users.profile_image',
                'users.display_name'
            ])
            ->where([
                'users.is_active' => User::ACTIVE,
                'users.is_deleted' => User::NOT_DELETED,
                'groups.status' => Group::STATUS_ACTIVE,
                'groups.cat_id' => $categoryId
            ])
            ->orderBy('groups.id', 'DESC')
            ->get();
    }

    public function getCountByCategoryId(int $categoryId, int $active)
    {
        return $this->model
            ->where([
                'status' => $active,
                'cat_id' => $categoryId
            ])
            ->count();
    }

    public function getActiveGroups(int $limit = 10)
    {
        return $this->model
            ->where([
                'status' => Group::STATUS_ACTIVE
            ])
            ->paginate($limit);
    }

    public function searchByKeyword(string $keyword, $limit = 10)
    {
        return $this->model
            ->where('group_name', 'like', "%$keyword%")
            ->where([
                'status' => Group::STATUS_ACTIVE
            ])
            ->paginate($limit);
    }

    public function getBySlug(string $slug)
    {
        return $this->model
            ->where([
                'group_uname' => $slug
            ])
            ->first();
    }

    public function getAllActiveCount()
    {
        return $this->model
            ->active()
            ->count();
    }

    public function getActiveByCategoryId(int $categoryId, int $limit = 10)
    {
        return $this->model
            ->where('cat_id', $categoryId)
            ->active()
            ->paginate($limit);
    }

    public function getUserGroupsList(int $userId, int $limit = 10)
    {
        return $this->model
            ->join('group_connections', 'group_connections.group_id', '=', 'groups.id')
            ->select(['groups.*'])
            ->where([
                'group_connections.user_id' => $userId
            ])
            ->active()
            ->orderByRaw("(CASE WHEN groups.user_id = $userId THEN 1 ELSE 0 END) DESC")
            ->paginate($limit);
    }

    public function getUserGroupsListCount(int $userId)
    {
        return $this->model
            ->join('group_connections', 'group_connections.group_id', '=', 'groups.id')
            ->select(['groups.id'])
            ->where([
                'group_connections.user_id' => $userId
            ])
            ->active()
            ->count();
    }

    public function deleteAdmin(Group $group, User $admin)
    {
        return $group->admins()->where(['user_id' => $admin->id])->delete();
    }
}