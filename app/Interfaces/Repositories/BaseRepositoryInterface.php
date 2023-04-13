<?php

namespace App\Models\Repositories;

interface BaseRepositoryInterface
{
    public function getById($id);

    public function update($id, $data);

    public function delete($id);

    public function all();
}
