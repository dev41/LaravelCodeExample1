<?php

namespace App\Repositories;

use App\Models\AboutUsContent;
use App\Models\File;
use App\Models\Group;
use App\Repositories\Contracts\AboutUsContentsRepositoryInterface;
use App\Repositories\Contracts\FilesRepositoryInterface;
use App\Traits\RepositoryTrait;
use App\User;

class FilesRepository implements FilesRepositoryInterface
{
    use RepositoryTrait;
    
    protected $model;

    public function __construct(File $model)
    {
        $this->model = $model;
    }

    public function insertMultiple(array $data)
    {
        return $this->model->insert($data);
    }

    public function getPostFiles(int $postId)
    {
        return $this->model
            ->whereIn('type', [
                File::TYPE_POSTS,
                File::TYPE_GROUP_POSTS,
                File::TYPE_ANOTHER_USER_POSTS
            ])
            ->where('object_id', $postId)
            ->get();
    }

    public function getUserPostImages(User $user, int $limit = 10)
    {
        return $this->model
            ->where([
                'user_id' => $user->id,
                'type' => File::TYPE_POSTS
            ])
            ->orWhere(function ($query) use ($user) {
                $query->where([
                    'connected_id' => $user->id,
                    'type' => File::TYPE_ANOTHER_USER_POSTS
                ]);
            })
            ->orderBy('created_at', 'DESC')
            ->paginate($limit);
    }

    public function getGroupPostImages(Group $group, int $limit = 10)
    {
        return $this->model
            ->whereIn('type', [
                File::TYPE_GROUP_POSTS
            ])
            ->where('connected_id', $group->id)
            ->orderBy('created_at', 'DESC')
            ->paginate($limit);
    }

    public function deletePostFiles(int $postId)
    {
        return $this->model
            ->whereIn('type', [
                File::TYPE_POSTS,
                File::TYPE_GROUP_POSTS,
                File::TYPE_ANOTHER_USER_POSTS
            ])
            ->where('object_id', $postId)
            ->delete();
    }

    public function getByType(string $type)
    {
        return $this->model
            ->where('type', $type)
            ->get();
    }
}