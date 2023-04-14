<?php

namespace App\Interfaces\Repositories;

interface LoanRepositoryInterface extends BaseRepositoryInterface
{
    public function getByUserId($userId, $skip = 0, $limit = 10);

    public function getPaidAmount($id);
}
