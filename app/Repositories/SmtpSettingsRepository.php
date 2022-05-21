<?php

namespace App\Repositories;

use App\Models\Settings;
use App\Models\SmtpSettings;
use App\Repositories\Contracts\SettingsRepositoryInterface;
use App\Repositories\Contracts\SmtpSettingsRepositoryInterface;
use App\Traits\RepositoryTrait;

class SmtpSettingsRepository implements SmtpSettingsRepositoryInterface
{
    use RepositoryTrait;
    
    protected $model;

    public function __construct(SmtpSettings $settings)
    {
        $this->model = $settings;
    }

    /**
     * Get site smtp settings
     * @return mixed
     */
    public function getSmtpSettings()
    {
        return $this->model->find(1);
    }
}