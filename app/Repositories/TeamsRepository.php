<?php

namespace App\Repositories;

use App\Models\AboutUsContent;
use App\Models\Team;
use App\Repositories\Contracts\AboutUsContentsRepositoryInterface;
use App\Repositories\Contracts\TeamsRepositoryInterface;
use App\Traits\RepositoryTrait;

class TeamsRepository implements TeamsRepositoryInterface
{
    use RepositoryTrait;
    
    protected $model;

    public function __construct(Team $model)
    {
        $this->model = $model;
    }

    public function getAllActive()
    {
        return $this->model
            ->where([
                'is_active' => Team::ACTIVE,
                'is_deleted' => Team::NOT_DELETED
            ])
            ->orderBy('id', 'DESC')
            ->get();
    }
}