<?php

namespace App\Repositories;

use App\Models\Mentorship;
use App\Repositories\Contracts\MentorshipsRepositoryInterface;
use App\Traits\RepositoryTrait;

class MentorshipsRepository implements MentorshipsRepositoryInterface
{
    use RepositoryTrait;
    
    protected $model;

    public function __construct(Mentorship $model)
    {
        $this->model = $model;
    }

    public function getQueryByKeyword($keyword)
    {
        $query = $this->model
            ->where([
                'is_active' => Mentorship::IS_ACTIVE,
                'is_deleted' => Mentorship::IS_NOT_DELETED
            ]);

        if ($keyword != '') {
            $query->where('name', 'like', '%' . addslashes($keyword) . '%');
        }

        return $query;
    }

    public function getAllActive()
    {
        return $this->model
            ->where([
                'is_active' => Mentorship::IS_ACTIVE,
                'is_deleted' => Mentorship::IS_NOT_DELETED
            ])
            ->orderBy('id', 'DESC')
            ->get();
    }

    public function getActiveById(int $mentorshipId)
    {
        return $this->model
            ->where([
                'is_active' => Mentorship::IS_ACTIVE,
                'is_deleted' => Mentorship::IS_NOT_DELETED,
                'id' => $mentorshipId
            ])
            ->first();
    }
}