<?php

namespace App\Repositories;

use App\Models\Magazine;
use App\Models\MagazineCategory;
use App\Repositories\Contracts\MagazineCategoriesRepositoryInterface;
use App\Traits\RepositoryTrait;

class MagazineCategoriesRepository implements MagazineCategoriesRepositoryInterface
{
    use RepositoryTrait;
    
    protected $model;

    public function __construct(MagazineCategory $model)
    {
        $this->model = $model;
    }

    public function getActiveByType($type, $order = false)
    {
        $query = $this->model
            ->where([
                'type' => $type
            ])
            ->active();

        if ($order) {
            $query->orderBy($order, 'DESC');
        }

        return $query->get();
    }

    public function getArticleBySlug(string $slug)
    {
        return $this->model
            ->join('magazines', 'magazine_categories.id', '=', 'magazines.mag_category_id')
            ->select([
                'magazines.*', 'magazine_categories.name AS categoryname'
            ])
            ->where([
                'magazines.slug' => $slug,
                'magazines.is_active' => Magazine::STATUS_ACTIVE,
                'magazines.type' => Magazine::TYPE_ARTICLE,
                'magazines.is_delete' => Magazine::NOT_DELETED
            ])
            ->first();
    }

    public function getActiveBySlug(string $slug)
    {
        return $this->model
            ->where([
                'is_active' => MagazineCategory::STATUS_ACTIVE,
                'type' => MagazineCategory::TYPE_ARTICLE,
                'slug' => $slug
            ])
            ->first();
    }

    public function getAllActiveBySlug(string $slug)
    {
        return $this->model
            ->where([
                'is_active' => MagazineCategory::STATUS_ACTIVE,
                'type' => MagazineCategory::TYPE_ARTICLE,
                'slug' => $slug
            ])
            ->get();
    }

    public function getActiveBySlugAndType(string $slug, int $type)
    {
        return $this->model
            ->where([
                'is_active' => MagazineCategory::STATUS_ACTIVE,
                'type' => $type,
                'slug' => $slug
            ])
            ->first();
    }

    public function getAllActiveBySlugAndType(string $slug, int $type)
    {
        return $this->model
            ->where([
                'is_active' => MagazineCategory::STATUS_ACTIVE,
                'type' => $type,
                'slug' => $slug
            ])
            ->get();
    }
}