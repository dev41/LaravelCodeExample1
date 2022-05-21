<?php

namespace App\Repositories;

use App\Models\MessageLink;
use App\Repositories\Contracts\MessageLinksRepositoryInterface;
use App\Traits\RepositoryTrait;

class MessageLinksRepository implements MessageLinksRepositoryInterface
{
    use RepositoryTrait;
    
    protected $model;

    public function __construct(MessageLink $model)
    {
        $this->model = $model;
    }

    public function getAllByMessageId(int $messageId)
    {
        return $this->model
            ->where('message_id', $messageId)
            ->get();
    }
}