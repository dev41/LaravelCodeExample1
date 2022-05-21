<?php

namespace App\Repositories;

use App\Models\MentorshipContent;
use App\Repositories\Contracts\MentorshipsContentsRepositoryInterface;
use App\Repositories\Contracts\StaticPagesContentsRepositoryInterface;
use App\Traits\RepositoryTrait;
use App\Traits\StaticContentTrait;

class MentorshipsContentsRepository implements MentorshipsContentsRepositoryInterface, StaticPagesContentsRepositoryInterface
{
    use RepositoryTrait;
    use StaticContentTrait;
    
    protected $model;

    public function __construct(MentorshipContent $model)
    {
        $this->model = $model;
    }

}