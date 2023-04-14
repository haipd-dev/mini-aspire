<?php

namespace App\Interfaces\Repositories;

interface BaseRepositoryInterface
{
    public function getModel();

    public function find($id);

    public function create(array $data);

    public function update($id, array $data);

    public function delete($id);

    public function getAll();
}
