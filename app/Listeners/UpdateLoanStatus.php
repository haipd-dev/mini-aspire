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
        $loan = $this->loanRepository->find($loanId);
        $paidAmount = $this->loanRepository->getPaidAmount($loanId);
        $isPaid = $paidAmount >= $loan->amount;
        $status = Loan::STATUS_PARTIAL_PAID;
        if ($isPaid) {
            $this->changeAllPendingRepaymentsToAutoPaid($loanId);
            $status = Loan::STATUS_PAID;
        }
        $this->loanRepository->update($loanId, ['status' => $status]);
    }

    private function changeAllPendingRepaymentsToAutoPaid($loanId)
    {
        $this->loanRepaymentRepository->massUpdate(
            ['loan_id' => $loanId, 'status' => LoanRepayment::STATUS_PENDING],
            ['status' => LoanRepayment::STATUS_AUTO_PAID]
        );
    }
}
