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
}
