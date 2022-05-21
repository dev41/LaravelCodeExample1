<?php

namespace App\Repositories\Contracts;

interface PrivacySettingsRepositoryInterface
{
    public function getByKey($value);

    public function getAllActive();
}