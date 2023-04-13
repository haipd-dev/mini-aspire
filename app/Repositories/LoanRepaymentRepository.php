<?php

namespace App\Repositories;

use App\Interfaces\Repositories\LoanRepaymentRepositoryInterface;
use App\Models\LoanRepayment;

class LoanRepaymentRepository extends BaseRepository  implements LoanRepaymentRepositoryInterface
{

    public function getModel()
    {
        return LoanRepayment::class;
    }
}
