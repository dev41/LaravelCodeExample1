<?php

namespace App\Repositories;

use App\Models\GroupCategory;
use App\Repositories\Contracts\GroupCategoriesRepositoryInterface;
use App\Traits\RepositoryTrait;

class GroupCategoriesRepository implements GroupCategoriesRepositoryInterface
{
    use RepositoryTrait;
    
    protected $model;

    public function __construct(GroupCategory $model)
    {
        $this->model = $model;
    }

    public function getAllActive(int $limit = 10, bool $showAll = false)
    {
        $query = $this->model
            ->active()
            ->orderBy('title', 'ASC');

        if ($showAll) {
            return $query->get();
        }


        return $query->paginate($limit);
    }
}