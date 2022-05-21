<?php
namespace App\Contracts\Services;

interface ImageSaverInterface
{
    public function storeImage($image, int $objectId);
}