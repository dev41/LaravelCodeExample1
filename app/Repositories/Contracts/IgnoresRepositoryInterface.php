<?php

namespace App\Repositories\Contracts;

use App\User;

interface IgnoresRepositoryInterface
{
    public function getByKey($value);

    public function store($data);

    public function delete(array $conditions);

    public function getUserCommentsIgnores(User $user);
}