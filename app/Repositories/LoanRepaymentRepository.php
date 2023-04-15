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

    public function getByLoanId($loanId)
    {
        $query = $this->_model->newQuery()->where('loan_id', $loanId);

        return $query->get();
    }

    public function massUpdate($filter, $updateData)
    {
        $query = $this->_model->newQuery();
        foreach ($filter as $key => $value) {
            $query = $query->where($key, $value);
        }
        $query->update($updateData);
    }
}
