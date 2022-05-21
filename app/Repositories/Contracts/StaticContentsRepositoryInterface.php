<?php

namespace App\Repositories\Contracts;

use App\Contracts\CrudInterface;
use App\Models\StaticContent;

interface StaticContentsRepositoryInterface
{
    public function getByKey($value);

    public function getActiveByParentId(int $parentId);
}