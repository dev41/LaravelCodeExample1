<?php

namespace App\Repositories;

use App\Models\PrivacySettings;
use App\Repositories\Contracts\PrivacySettingsRepositoryInterface;
use App\Traits\RepositoryTrait;

class PrivacySettingsRepository implements PrivacySettingsRepositoryInterface
{
    use RepositoryTrait;
    
    protected $model;

    public function __construct(PrivacySettings $model)
    {
        $this->model = $model;
    }

    public function getAllActive()
    {
        return $this->model
            ->where([
                'status' => PrivacySettings::ACTIVE,
                'is_delete' => PrivacySettings::NOT_DELETED
            ])
            ->get();
    }

}