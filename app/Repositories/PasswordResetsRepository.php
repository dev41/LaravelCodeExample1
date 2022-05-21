<?php

namespace App\Repositories;

use App\Models\PasswordReset;
use App\Repositories\Contracts\PasswordResetsRepositoryInterface;
use App\Traits\RepositoryTrait;

class PasswordResetsRepository implements PasswordResetsRepositoryInterface
{
    use RepositoryTrait;
    
    protected $model;

    /**
     * PasswordResetsRepository constructor.
     * @param PasswordReset $passwordReset
     */
    public function __construct(PasswordReset $passwordReset)
    {
        $this->model = $passwordReset;
    }
}