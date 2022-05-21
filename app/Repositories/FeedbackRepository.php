<?php

namespace App\Repositories;

use App\Models\Feedback;
use App\Repositories\Contracts\FeedbackRepositoryInterface;
use App\Traits\RepositoryTrait;

class FeedbackRepository implements FeedbackRepositoryInterface
{
    use RepositoryTrait;
    
    protected $model;

    public function __construct(Feedback $model)
    {
        $this->model = $model;
    }
}