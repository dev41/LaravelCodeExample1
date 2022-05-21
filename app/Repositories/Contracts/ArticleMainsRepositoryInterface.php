<?php

namespace App\Repositories\Contracts;

interface ArticleMainsRepositoryInterface
{
    public function getByKey($value);

    public function getActiveById($articleMainId);
}