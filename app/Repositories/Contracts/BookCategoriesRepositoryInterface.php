<?php

namespace App\Repositories\Contracts;

interface BookCategoriesRepositoryInterface
{
    public function getByKey($value);

    public function getActiveCategories();

    public function getActiveById(int $categoryId);

    public function getActiveBySlug(string $slug);

    public function getBySlug(string $slug);
}