<?php

namespace App\Repositories\Contracts;

interface BookCommentsRepositoryInterface
{
    public function getByKey($value);

    public function store($data);

    public function getActiveByBookId(int $bookId, int $offset = 0, int $limit = 10);

    public function getCountByBookId(int $bookId);
}