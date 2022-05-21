<?php

namespace App\Repositories;

use App\Models\Content;
use App\Repositories\Contracts\ContentsRepositoryInterface;
use App\Traits\RepositoryTrait;

class ContentsRepository implements ContentsRepositoryInterface
{
    use RepositoryTrait;
    
    protected $model;

    public function __construct(Content $content)
    {
        $this->model = $content;
    }

    public function getBySlug($slug)
    {
        $query = $this->model
            ->where([
                'is_active' => Content::ACTIVE,
                'is_deleted' => Content::NOT_DELETED
            ]);

        if ($slug != 'all' && $slug != '') {
            $query->where([
                'slug' => $slug
            ]);
        }

        return $query->get();
    }

}