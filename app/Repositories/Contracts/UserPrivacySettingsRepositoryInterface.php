<?php

namespace App\Repositories\Contracts;

interface UserPrivacySettingsRepositoryInterface
{
    public function getByKey($value);

    public function getByUserId($userId);

    public function delete(array $data);

    public function store($data);

    public function all();
}