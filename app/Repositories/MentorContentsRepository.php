<?php

namespace App\Repositories;

use App\Models\MentorContent;
use App\Repositories\Contracts\MentorContentsRepositoryInterface;
use App\Traits\RepositoryTrait;

class MentorContentsRepository implements MentorContentsRepositoryInterface
{
    use RepositoryTrait;
    
    protected $model;

    public function __construct(MentorContent $model)
    {
        $this->model = $model;
    }

    public function getActiveByType(int $type)
    {
        return $this->model
            ->where([
                'is_active' => MentorContent::ACTIVE,
                'type' => $type
            ])
            ->first();
    }
}