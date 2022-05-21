<?php

namespace App\Repositories;

use App\Models\UserOneSignalPlayer;
use App\Repositories\Contracts\UserOneSignalPlayersRepositoryInterface;
use App\Traits\RepositoryTrait;

class UserOneSignalPlayersRepository implements UserOneSignalPlayersRepositoryInterface
{
    use RepositoryTrait;
    
    protected $model;

    public function __construct(UserOneSignalPlayer $model)
    {
        $this->model = $model;
    }

    /**
     * @param $playerId
     * @return \Illuminate\Support\Collection
     */
    public function getByPlayerId(string $playerId)
    {
        return $this->model->where([
            'player_id' => $playerId
        ])->get();
    }
}