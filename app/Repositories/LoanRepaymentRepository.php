<?php

namespace App\Repositories;

use App\Interfaces\Repositories\LoanRepaymentRepositoryInterface;
use App\Models\LoanRepayment;

class LoanRepaymentRepository extends BaseRepository implements LoanRepaymentRepositoryInterface
{

    public function getModel()
    {
        return LoanRepayment::class;
    }

    public function getByLoanId($loanId, $status = null)
    {
        $query = $this->_model->newQuery()->where('loan_id', $loanId);
        if ($status) {
            $query->where('status', $status);
        }
        return $query->get();
    }
}
