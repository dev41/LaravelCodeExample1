<?php

namespace App\Repositories\Contracts;

interface MentorContentsRepositoryInterface
{
    public function getByKey($value);

    public function getActiveByType(int $type);
}