<?php

namespace App\Repositories;

use App\Models\ShareStory;
use App\Repositories\Contracts\SharestoriesRepositoryInterface;
use App\Repositories\Contracts\StaticPagesContentsRepositoryInterface;
use App\Traits\RepositoryTrait;
use App\Traits\StaticContentTrait;

class SharestoriesRepository implements SharestoriesRepositoryInterface, StaticPagesContentsRepositoryInterface
{
    use RepositoryTrait;
    use StaticContentTrait;
    
    protected $model;

    public function __construct(ShareStory $model)
    {
        $this->model = $model;
    }

}