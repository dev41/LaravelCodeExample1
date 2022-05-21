<?php

namespace App\Repositories\Contracts;

interface MentorshipsRepositoryInterface
{
    public function getByKey($value);

    public function store($data);

    public function getQueryByKeyword($keyword);

    public function getAllActive();

    public function getActiveById(int $mentorshipId);
}