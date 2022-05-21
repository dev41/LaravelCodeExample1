<?php
namespace App\Contracts;

interface CrudInterface
{
    public function store($data);

    public function update($data, $conditions);

    public function delete(array $conditions);
}