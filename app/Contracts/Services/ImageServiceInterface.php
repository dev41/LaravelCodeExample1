<?php
namespace App\Contracts\Services;

interface ImageServiceInterface
{
    public function storeImage($image, int $objectId, ServiceInterface $service);

    public function store($image, int $objectId, ImageSaverInterface $service);
}