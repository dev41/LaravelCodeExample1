<?php

namespace App\Repositories;

use App\Models\Country;
use App\Repositories\Contracts\CountriesRepositoryInterface;
use App\Traits\RepositoryTrait;

class CountriesRepository implements CountriesRepositoryInterface
{
    use RepositoryTrait;
    
    protected $model;

    public function __construct(Country $model)
    {
        $this->model = $model;
    }

    public function getAllActiveCountries()
    {
        return $this->model
            ->where([
                'active' => Country::ACTIVE
            ])
            ->get([
                'countries_name', 'id'
            ]);
    }
}