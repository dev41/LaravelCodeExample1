<?php
namespace App\Contracts\Services;

interface ServiceInterface
{
    public function generatePermalink(string $title);

    public function storeImage($image, int $objectId);
}