<?php

namespace App\Repositories\Contracts;

interface FeedbackRepositoryInterface
{
    public function getByKey($value);

    public function store($data);
}