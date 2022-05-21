<?php

namespace App\Repositories\Contracts;

use App\Models\Group;
use App\User;

interface FilesRepositoryInterface
{
    public function getByKey($value);

    public function insertMultiple(array $data);

    public function getPostFiles(int $postId);

    public function getUserPostImages(User $user, int $limit = 10);

    public function getGroupPostImages(Group $group, int $limit = 10);

    public function deletePostFiles(int $postId);

    public function store($data);

    public function getByType(string $type);
}