<?php

namespace App\Repositories;

use App\Models\Settings;
use App\Repositories\Contracts\SettingsRepositoryInterface;
use App\Traits\RepositoryTrait;

class SettingsRepository implements SettingsRepositoryInterface
{
    use RepositoryTrait;
    
    protected $model;

    public function __construct(Settings $settings)
    {
        $this->model = $settings;
    }

    /**
     * Get all site settings
     * @return mixed
     */
    public function getSiteSettings()
    {
        return $this->model->find(1);
    }
}