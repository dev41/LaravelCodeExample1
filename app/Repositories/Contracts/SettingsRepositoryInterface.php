<?php

namespace App\Repositories\Contracts;

interface SettingsRepositoryInterface
{
    public function getByKey($value);

    public function getSiteSettings();
}