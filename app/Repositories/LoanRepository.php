<?php

namespace App\Repositories;

use App\Interfaces\Repositories\LoanRepaymentRepositoryInterface;
use App\Interfaces\Repositories\LoanRepositoryInterface;
use App\Models\Loan;
use App\Models\LoanRepayment;

class LoanRepository extends BaseRepository implements LoanRepositoryInterface
{

    protected $loanRepaymentRepository;

    public function __construct(
        LoanRepaymentRepositoryInterface $loanRepaymentRepository
    )
    {
        parent::__construct();
        $this->loanRepaymentRepository = $loanRepaymentRepository;
    }

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

    public function getPaidAmount($id)
    {
        $repayments = $this->loanRepaymentRepository->getByLoanId($id, LoanRepayment::STATUS_PAID);
        $amount = 0;
        foreach ($repayments as $repayment) {
            $amount += $repayment->paid_amount;
        }
        return $amount;
    }
}
