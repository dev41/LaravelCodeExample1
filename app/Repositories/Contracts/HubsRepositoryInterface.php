<?php

namespace App\Repositories\Contracts;

use App\Models\Hub;
use App\User;

interface HubsRepositoryInterface
{
    public function getByKey($value);

    public function update($data, $conditions);

    public function getByPermalink(string $permalink);

    public function getAllActiveHubs(array $conditions, int $limit = 10);

    public function getQueryByKeyword($keyword);

    public function getUserHubs(User $user, int $limit = 10);

    public function updateHub(Hub $hub, array $data) : Hub;
}