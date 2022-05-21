<?php

namespace App\Repositories\Contracts;

interface BooksRepositoryInterface
{
    public function getByKey($value);

    public function delete(array $conditions);

    public function getQueryByKeyword($keyword);

    public function getActiveById(int $bookId);

    public function getAllActive();

    public function getAllActiveCount();

    public function getActiveBySlug(string $slug);

    public function getBooks(array $conditions);
}