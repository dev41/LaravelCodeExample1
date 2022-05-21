<?php

namespace App\Services;

use App\Contracts\Services\ServiceInterface;
use App\Helpers\AzureBlob;
use App\Repositories\Contracts\HubsRepositoryInterface;
use App\Repositories\Contracts\UserRepositoryInterface;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\ImageManager;

class UserService {
    private $usersRepository;

    public function __construct(UserRepositoryInterface $usersRepository)
    {
        $this->usersRepository = $usersRepository;
    }

    public function generatePermalink(string $name)
    {
        $permalink = str_slug($name);

        if ($hub = $this->usersRepository->getByDisplayName($permalink)) {
            $permalink = str_slug($permalink . ' ' . str_random(7));
        }

        return $permalink;
    }
}