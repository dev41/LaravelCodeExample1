<?php
namespace App\Contracts\Sharing;

interface ShareableInterface
{
    public function getData(string $permalink) : array;
}
