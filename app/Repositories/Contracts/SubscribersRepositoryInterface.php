<?php

namespace App\Repositories\Contracts;

interface SubscribersRepositoryInterface
{
    public function getByKey($value);

    public function store($data);

    public function getByEmail($email);
}