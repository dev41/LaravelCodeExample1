<?php

namespace App\Repositories\Contracts;

interface HubCategoriesRepositoryInterface
{
    public function getByKey($value);

    public function all();

    public function getAllActive(int $limit = 10, bool $showAll = false);
}