<?php

namespace App\Repositories\Contracts;

interface PasswordResetsRepositoryInterface
{
    public function getByKey($value);

    public function store($data);

    public function delete(array $conditions);
}