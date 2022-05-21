<?php

namespace App\Repositories\Contracts;

interface MessageLinksRepositoryInterface
{
    public function getByKey($value);

    public function store($data);

    public function getAllByMessageId(int $messageId);
}