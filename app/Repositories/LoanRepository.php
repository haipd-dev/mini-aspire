<?php

namespace App\Repositories;

use App\Interfaces\Repositories\LoanRepositoryInterface;
use App\Models\Loan;

class LoanRepository extends BaseRepository implements LoanRepositoryInterface
{
    public function getModel()
    {
        return Loan::class;
    }

    public function getByUserId($userId, $skip = 0, $limit = 10)
    {
        $query = $this->_model->newQuery()->where('user_id', $userId)->skip($skip)->limit($limit)
            ->with(['repayments'])
            ->orderBy('created_at', 'desc');

        return $query->get();
    }
}
