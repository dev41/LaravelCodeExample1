<?php

namespace App\Repositories\Contracts;

interface AdvisoryMappingsRepositoryInterface
{
    public function getByKey($value);

    public function getAllActiveByAdvisoryId(int $advisoryId);
}