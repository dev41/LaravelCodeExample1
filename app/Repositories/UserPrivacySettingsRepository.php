<?php

namespace App\Repositories;

use App\Models\Settings;
use App\Models\UserPrivacySettings;
use App\Repositories\Contracts\SettingsRepositoryInterface;
use App\Repositories\Contracts\UserPrivacySettingsRepositoryInterface;
use App\Traits\RepositoryTrait;

class UserPrivacySettingsRepository implements UserPrivacySettingsRepositoryInterface
{
    use RepositoryTrait;
    
    protected $model;

    public function __construct(UserPrivacySettings $userPrivacySettings)
    {
        $this->model = $userPrivacySettings;
    }

    /**
     * Get user privacy settings by id
     * @param $userId
     * @return mixed
     */
    public function getByUserId($userId)
    {
        return $this->model->where([
            'user_id' => $userId
        ])->first();
    }
}