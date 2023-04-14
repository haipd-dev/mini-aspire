<?php

namespace App\Listeners;

use App\Events\LoanRepaymentPaid;
use App\Interfaces\Repositories\LoanRepaymentRepositoryInterface;
use App\Interfaces\Repositories\LoanRepositoryInterface;
use App\Models\Loan;
use App\Models\LoanRepayment;

class UpdateLoanStatus
{
    protected $loanRepository;

    protected $loanRepaymentRepository;

    public function __construct(
        LoanRepositoryInterface          $loanRepository,
        LoanRepaymentRepositoryInterface $loanRepaymentRepository
    )
    {
        $this->loanRepository = $loanRepository;
        $this->loanRepaymentRepository = $loanRepaymentRepository;
    }

    /**
     * Handle the event.
     */
    public function handle(LoanRepaymentPaid $event): void
    {
        $repayment = $event->getLoanRepayment();
        $loanId = $repayment->loan_id;
        $repayments = $this->loanRepaymentRepository->getByLoanId($loanId);
        $status = Loan::STATUS_PAID;
        foreach ($repayments as $item) {
            if ($item->status == LoanRepayment::STATUS_PENDING) {
                $status = Loan::STATUS_PARTIAL_PAID;
            }
        }
        $this->loanRepository->update($loanId, ['status' => $status]);
    }
}
