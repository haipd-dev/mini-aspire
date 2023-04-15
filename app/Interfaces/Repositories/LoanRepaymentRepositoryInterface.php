<?php

namespace App\Interfaces\Repositories;

interface LoanRepaymentRepositoryInterface extends BaseRepositoryInterface
{
    public function getByLoanId($loanId);

    public function massUpdate($filter, $updateData);
}
