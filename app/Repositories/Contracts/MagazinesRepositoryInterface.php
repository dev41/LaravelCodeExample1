<?php

namespace App\Repositories\Contracts;

interface MagazinesRepositoryInterface
{
    public function getByKey($value);

    public function store($data);

    public function getActiveByTypeAndCategoryId($type, $categoryId, $limit = 15);

    public function getLastArticlesByType($type, $limit = 4);

    public function getQueryByKeyword($keyword, $type);

    public function getActiveArticleById(int $articleId);

    public function getActiveArticles(int $limit = 10);

    public function getActiveByCategoryId(int $categoryId);

    public function getActiveByCategoryIdAndType(int $categoryId, int $type);

    public function getActiveCountByCategoryIdAndType(int $categoryId, int $type);

    public function getActiveCountByType(int $type);

    public function getByIdAndUserId(int $magazineId, int $userId);

    public function getFeaturedArticles(int $limit = 3);

    public function getActiveEventBySlug(string $slug);

    public function getLastActiveEventByCategoryId(int $categoryId);

    public function getArticleBySlug(string $slug);

    public function getCategoryArticles(int $categoryId, int $limit = 10);
}