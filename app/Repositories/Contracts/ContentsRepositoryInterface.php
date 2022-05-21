<?php

namespace App\Repositories\Contracts;

interface ContentsRepositoryInterface
{
    public function getByKey($value);

    public function getBySlug($slug);
}