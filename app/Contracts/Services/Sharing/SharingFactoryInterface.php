<?php
namespace App\Contracts\Sharing;

interface SharingFactoryInterface
{
    public function make(string $url) : ShareableInterface;
}
