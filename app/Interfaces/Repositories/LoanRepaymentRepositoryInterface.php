<?php

namespace App\Interfaces\Repositories;

use App\Interfaces\Repositories\BaseRepositoryInterface;

interface LoanRepaymentRepositoryInterface extends BaseRepositoryInterface
{
    public function getByLoanId($loanId, $status = null);
}
