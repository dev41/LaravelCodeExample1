<?php

namespace App\Repositories\Contracts;

interface EmailNotificationSettingsRepositoryInterface
{
    public function getByKey($value);

    public function all();

    public function getByStatus(int $status);

    public function getByIds(array $ids);

    public function getByKeyword(string $key);
}