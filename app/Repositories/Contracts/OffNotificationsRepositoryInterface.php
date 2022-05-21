<?php

namespace App\Repositories\Contracts;

interface OffNotificationsRepositoryInterface
{
    public function getByKey($value);

    public function delete(array $conditions);

    public function getByUserAndPost($user, $post);
}