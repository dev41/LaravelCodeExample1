<?php

namespace App\Repositories\Contracts;

interface UserOneSignalPlayersRepositoryInterface
{
    public function getByKey($value);

    public function delete(array $conditions);

    public function getByPlayerId(string $playerId);
}