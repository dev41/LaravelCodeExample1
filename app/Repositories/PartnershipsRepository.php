<?php

namespace App\Repositories;

use App\Models\Partnership;
use App\Repositories\Contracts\PartnershipsRepositoryInterface;
use App\Repositories\Contracts\StaticPagesContentsRepositoryInterface;
use App\Traits\RepositoryTrait;
use App\Traits\StaticContentTrait;

class PartnershipsRepository implements PartnershipsRepositoryInterface, StaticPagesContentsRepositoryInterface
{
    use RepositoryTrait;
    use StaticContentTrait;
    
    protected $model;

    public function __construct(Partnership $model)
    {
        $this->model = $model;
    }

}