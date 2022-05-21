<?php
namespace App\Contracts\Services;

interface PermalinkServiceInterface
{
    public function generate(string $title, ServiceInterface $service);
}