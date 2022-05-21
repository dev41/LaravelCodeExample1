<?php

namespace App\Repositories;

use App\Models\OffNotification;
use App\Repositories\Contracts\OffNotificationsRepositoryInterface;
use App\Traits\RepositoryTrait;

class OffNotificationsRepository implements OffNotificationsRepositoryInterface
{
    use RepositoryTrait;
    
    protected $model;

    /**
     * OffNotificationsRepository constructor.
     * @param OffNotification $model
     */
    public function __construct(OffNotification $model)
    {
        $this->model = $model;
    }

    public function getByUserAndPost($user, $post)
    {
        return $this->model
            ->where([
                'user_id' => $user->id,
                'post_id' => $post->id
            ])
            ->first();
    }
}