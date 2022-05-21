<?php

namespace App\Repositories;

use App\Models\Magazine;
use App\Repositories\Contracts\MagazinesRepositoryInterface;
use App\Traits\RepositoryTrait;

class MagazinesRepository implements MagazinesRepositoryInterface
{
    use RepositoryTrait;
    
    protected $model;

    public function __construct(Magazine $model)
    {
        $this->model = $model;
    }

    public function getActiveByTypeAndCategoryId($type, $categoryId, $limit = 15)
    {
        return $this->model
            ->where([
                'type' => $type,
                'mag_category_id' => $categoryId
            ])
            ->active()
            ->orderBy('id', 'DESC')
            ->take($limit)
            ->get();
    }

    public function getLastArticlesByType($type, $limit = 3)
    {
        return $this->model
            ->where([
                'is_active' => Magazine::STATUS_ACTIVE,
                'type' => $type
            ])
            ->orderBy('id', 'DESC')
            ->take($limit)
            ->get();
    }

    public function getQueryByKeyword($keyword, $type)
    {
        $query = $this->model
            ->where([
                'type' => $type,
                'is_delete' => Magazine::NOT_DELETED,
                'is_active' => Magazine::STATUS_ACTIVE
            ]);

        if ($keyword != '') {
            $query->where(function ($query) use ($keyword) {
                $query->where('title', 'like', '%' . addslashes($keyword) . '%')
                    ->orWhere('author_name', 'like', '%' . addslashes($keyword) . '%');
            });
        }

        return $query;
    }

    public function getActiveArticleById(int $articleId)
    {
        return $this->model
            ->where([
                'is_active' => Magazine::STATUS_ACTIVE,
                'type' => Magazine::TYPE_ARTICLE,
                'id' => $articleId,
                'is_delete' => Magazine::NOT_DELETED
            ])
            ->first();
    }

    public function getActiveArticles(int $limit = 10)
    {
        return $this->model
            ->where([
                'is_active' => Magazine::STATUS_ACTIVE,
                'type' => Magazine::TYPE_ARTICLE,
                'is_delete' => Magazine::NOT_DELETED
            ])
            ->paginate($limit);
    }

    public function getActiveByCategoryId(int $categoryId)
    {
        return $this->model
            ->where([
                'mag_category_id' => $categoryId,
                'is_active' => Magazine::STATUS_ACTIVE,
                'is_delete' => Magazine::NOT_DELETED
            ])
            ->orderBy('id', 'DESC')
            ->first();
    }

    public function getActiveByCategoryIdAndType(int $categoryId, int $type)
    {
        return $this->model
            ->where([
                'mag_category_id' => $categoryId,
                'is_active' => Magazine::STATUS_ACTIVE,
                'is_delete' => Magazine::NOT_DELETED,
                'type' => $type
            ])
            ->orderBy('id', 'DESC')
            ->get();
    }

    public function getActiveCountByCategoryIdAndType(int $categoryId, int $type)
    {
        return $this->model
            ->where([
                'mag_category_id' => $categoryId,
                'type' => $type
            ])
            ->active()
            ->count();
    }

    public function getByIdAndUserId(int $magazineId, int $userId)
    {
        return $this->model
            ->where([
                'id' => $magazineId,
                'user_id' => $userId
            ])
            ->first();
    }

    public function getFeaturedArticles(int $limit = 3)
    {
        return $this->model
            ->where([
                'is_active' => Magazine::STATUS_ACTIVE,
                'is_delete' => Magazine::NOT_DELETED,
                'type' => Magazine::TYPE_ARTICLE,
                'is_featured' => Magazine::FEATURED
            ])
            ->inRandomOrder()
            ->take($limit)
            ->get();
    }

    public function getActiveEventBySlug(string $slug)
    {
        return $this->model
            ->where([
                'is_active' => Magazine::STATUS_ACTIVE,
                'type' => Magazine::TYPE_EVENT,
                'slug' => $slug
            ])
            ->first();
    }

    public function getLastActiveEventByCategoryId(int $categoryId)
    {
        return $this->model
            ->where([
                'mag_category_id' => $categoryId,
                'is_active' => Magazine::STATUS_ACTIVE,
                'type' => Magazine::TYPE_EVENT
            ])
            ->orderBy('id', 'DESC')
            ->first();
    }

    public function getArticleBySlug(string $slug)
    {
        return $this->model
            ->where([
                'magazines.slug' => $slug,
                'magazines.is_active' => Magazine::STATUS_ACTIVE,
                'magazines.type' => Magazine::TYPE_ARTICLE,
                'magazines.is_delete' => Magazine::NOT_DELETED
            ])
            ->first();
    }

    public function getActiveCountByType(int $type)
    {
        return $this->model
            ->where([
                'type' => $type
            ])
            ->active()
            ->count();
    }

    public function getCategoryArticles(int $categoryId, int $limit = 10)
    {
        return $this->model
            ->where([
                'mag_category_id' => $categoryId,
                'is_active' => Magazine::STATUS_ACTIVE,
                'is_delete' => Magazine::NOT_DELETED,
                'type' => Magazine::TYPE_ARTICLE
            ])
            ->orderBy('id', 'DESC')
            ->paginate($limit);
    }
}