<?php

namespace App\Repositories\Contracts;

interface CountriesRepositoryInterface
{
    public function getByKey($value);

    public function getAllActiveCountries();
}