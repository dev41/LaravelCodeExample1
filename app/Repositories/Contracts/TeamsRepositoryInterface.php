<?php

namespace App\Repositories\Contracts;

interface TeamsRepositoryInterface
{
    public function getByKey($value);

    public function store($data);

    public function getAllActive();
}