<?php

namespace App\Repositories\Contracts;

interface AdvisoriesRepositoryInterface
{
    public function getByKey($value);

    public function getAllActive();
}