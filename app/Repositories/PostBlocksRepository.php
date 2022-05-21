<?php

namespace App\Repositories;

use App\Models\Group;
use App\Models\PostBlock;
use App\Repositories\Contracts\PostBlocksRepositoryInterface;
use App\Traits\RepositoryTrait;
use App\User;

class PostBlocksRepository implements PostBlocksRepositoryInterface
{
    use RepositoryTrait;
    
    protected $model;

    public function __construct(PostBlock $postBlock)
    {
        $this->model = $postBlock;
    }

    public function getByGroupIdAndGroupType($groupId, $groupType)
    {
        return $this->model->where([
            'group_id' => $groupId,
            'group_type' => $groupType
        ])
            ->orderBy('id', 'DESC')
            ->get(['user_id']);
    }

    public function getByGroupIdWithUserInfo(int $groupId)
    {
        return $this->model
            ->leftJoin('users', 'post_blocks.user_id', '=', 'users.id')
            ->select([
                '*', 'post_blocks.id AS pid'
            ])
            ->where([
                'post_blocks.group_id' => $groupId,
                'post_blocks.group_type' => Group::GROUP_TYPE_PUBLIC,
                'users.is_deleted' => User::NOT_DELETED
            ])
            ->get();
    }

    public function getBlockedPost(int $postId, int $groupId, int $userId, int $groupType)
    {
        return $this->model
            ->where([
                'post_id' => $postId,
                'group_id' => $groupId,
                'user_id' => $userId,
                'group_type' => $groupType,
                'type' => PostBlock::TYPE_POST
            ])
            ->first();
    }

    public function getBlockedComment(int $postId, int $groupId, int $userId, int $groupType)
    {
        return $this->model
            ->where([
                'post_id' => $postId,
                'group_id' => $groupId,
                'user_id' => $userId,
                'group_type' => $groupType,
                'type' => PostBlock::TYPE_COMMENT
            ])
            ->first();
    }

    public function getByGroupIdUserIdAndGroupType(int $groupId, int $userId, int $groupType)
    {
        return $this->model
            ->where([
                'group_id' => $groupId,
                'user_id' => $userId,
                'group_type' => $groupType
            ])
            ->first();
    }
}