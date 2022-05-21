<?php

namespace App\Repositories;

use App\Models\HubCategory;
use App\Repositories\Contracts\HubCategoriesRepositoryInterface;
use App\Traits\RepositoryTrait;
use Illuminate\Support\Facades\DB;

class HubCategoriesRepository implements HubCategoriesRepositoryInterface
{
    use RepositoryTrait;
    
    protected $model;

    public function __construct(HubCategory $model)
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