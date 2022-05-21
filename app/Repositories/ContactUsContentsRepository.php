<?php

namespace App\Repositories;

use App\Models\ContactUsContent;
use App\Repositories\Contracts\ContactUsContentsRepositoryInterface;
use App\Repositories\Contracts\StaticPagesContentsRepositoryInterface;
use App\Traits\RepositoryTrait;
use App\Traits\StaticContentTrait;

class ContactUsContentsRepository implements ContactUsContentsRepositoryInterface, StaticPagesContentsRepositoryInterface
{
    use RepositoryTrait;
    use StaticContentTrait;
    
    protected $model;

    public function __construct(ContactUsContent $model)
    {
        $this->model = $model;
    }

}