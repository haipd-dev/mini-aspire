<?php

namespace App\Interfaces\Repositories;

interface LoanRepaymentRepositoryInterface extends BaseRepositoryInterface
{
    public function getByLoanId($loanId, $status = null);

    public function massUpdate($filter, $updateData);
}
