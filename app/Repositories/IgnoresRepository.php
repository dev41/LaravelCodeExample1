<?php

namespace App\Repositories;

use App\Models\Ignore;
use App\Repositories\Contracts\IgnoresRepositoryInterface;
use App\Traits\RepositoryTrait;
use App\User;

class IgnoresRepository implements IgnoresRepositoryInterface
{
    use RepositoryTrait;
    
    protected $model;

    public function __construct(Ignore $model)
    {
        $this->model = $model;
    }

    public function getUserCommentsIgnores(User $user)
    {
        return $this->model
            ->where('user_id', $user->id)
            ->whereNotNull('comment_id')
            ->get();
    }
}