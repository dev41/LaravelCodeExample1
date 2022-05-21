<?php

namespace App\Repositories;

use App\Models\Subscriber;
use App\Repositories\Contracts\SubscribersRepositoryInterface;
use App\Traits\RepositoryTrait;

class SubscribersRepository implements SubscribersRepositoryInterface
{
    use RepositoryTrait;
    
    protected $model;

    public function __construct(Subscriber $model)
    {
        $this->model = $model;
    }

    public function getByEmail($email)
    {
        return $this->model
            ->where([
                'email' => $email
            ])
            ->first();
    }
}