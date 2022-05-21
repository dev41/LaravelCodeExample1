<?php

namespace App\Repositories\Contracts;

interface HubInvitesRepositoryInterface
{
    public function getByKey($value);

    public function store($data);

    public function update($data, $conditions);

    public function getByUserId($userId);

    public function getByHubIdAndUserId($hubId, $userId);

    public function delete(array $conditions);
}