<?php

namespace App\Repositories\Contracts;

interface MagazineCategoriesRepositoryInterface
{
    public function getByKey($value);

    public function getActiveByType($type, $order = false);

    public function getArticleBySlug(string $slug);

    public function getActiveBySlug(string $slug);

    public function getAllActiveBySlug(string $slug);

    public function getActiveBySlugAndType(string $slug, int $type);

    public function getAllActiveBySlugAndType(string $slug, int $type);
}