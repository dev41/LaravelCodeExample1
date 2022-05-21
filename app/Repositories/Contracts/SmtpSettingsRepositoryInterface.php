<?php

namespace App\Repositories\Contracts;

interface SmtpSettingsRepositoryInterface
{
    public function getByKey($value);

    public function getSmtpSettings();
}