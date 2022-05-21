<?php

namespace App\Repositories;

use App\Models\HubInvite;
use App\Repositories\Contracts\HubInvitesRepositoryInterface;
use App\Traits\RepositoryTrait;

class HubInvitesRepository implements HubInvitesRepositoryInterface
{
    use RepositoryTrait;
    
    protected $model;

    public function __construct(HubInvite $hubInvite)
    {
        $this->model = $hubInvite;
    }

    public function getByUserId($userId)
    {
        return $this->model
            ->where([
                'user_id' => $userId
            ])->get();
    }

    public function getByHubIdAndUserId($hubId, $userId)
    {
        return $this->model
            ->where([
                'hub_id' => $hubId,
                'user_id' => $userId
            ])->first();
    }
}