<?php

namespace App\Services;

use App\Contracts\Services\PermalinkServiceInterface;
use App\Contracts\Services\ServiceInterface;

class PermalinkServiceService implements PermalinkServiceInterface {
    public function generate(string $title, ServiceInterface $service)
    {
        return $service->generatePermalink($title);
    }
}