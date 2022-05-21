<?php

namespace App\Repositories;

use App\Models\EmailNotificationSettings;
use App\Repositories\Contracts\EmailNotificationSettingsRepositoryInterface;
use App\Traits\RepositoryTrait;

class EmailNotificationSettingsRepository implements EmailNotificationSettingsRepositoryInterface
{
    use RepositoryTrait;
    
    protected $model;

    public function __construct(EmailNotificationSettings $model)
    {
        $this->model = $model;
    }

    public function getByStatus(int $status)
    {
        return $this->model
            ->where([
                'status' => $status
            ])
            ->get();
    }

    public function getByIds(array $ids)
    {
        return $this->model
            ->whereIn('id', $ids)
            ->get();
    }

    public function getByKeyword(string $key)
    {
        return $this->model
            ->where([
                'key' => $key
            ])
            ->first();
    }
}