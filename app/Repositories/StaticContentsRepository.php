<?php

namespace App\Repositories;

use App\Contracts\CrudInterface;
use App\Models\StaticContent;
use App\Repositories\Contracts\StaticContentsRepositoryInterface;
use App\Traits\RepositoryTrait;

class StaticContentsRepository implements StaticContentsRepositoryInterface, CrudInterface
{
    use RepositoryTrait;
    
    protected $model;

    public function __construct(StaticContent $model)
    {
        $this->model = $model;
    }

    public function getActiveByParentId(int $parentId)
    {
        return $this->model
            ->where([
                'is_active' => StaticContent::ACTIVE,
                'parent_id' => $parentId
            ])
            ->get();
    }
}