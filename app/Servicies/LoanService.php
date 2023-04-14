<?php

namespace App\Servicies;

use App\Events\LoanRepaymentPaid;
use App\Exceptions\InvalidInputException;
use App\Exceptions\NotAllowException;
use App\Helpers\LoanHelper;
use App\Interfaces\Repositories\LoanRepaymentRepositoryInterface;
use App\Interfaces\Repositories\LoanRepositoryInterface;
use App\Interfaces\Services\LoanServiceInterface;
use App\Models\Loan;
use App\Models\LoanRepayment;
use Illuminate\Support\Facades\DB;

class LoanService implements LoanServiceInterface
{

    protected $loanRepository;

    protected $loanRepaymentRepository;

    protected $loanHelper;

    public function __construct(
        LoanRepositoryInterface          $loanRepository,
        LoanRepaymentRepositoryInterface $loanRepaymentRepository
    )
    {
        $this->loanRepository = $loanRepository;
        $this->loanRepaymentRepository = $loanRepaymentRepository;
        $this->loanHelper = new LoanHelper();
    }

    public function createLoan($userId, float $amount, int $term, $submitDate = null)
    {
        if (!$submitDate) {
            $submitDate = now()->format('Y-m-d');
        }
        if ($amount <= 0 || $term <= 0) {
            throw new InvalidInputException("The amount and the term should be larger than 0");
        }
        $checkDate = \DateTime::createFromFormat('Y-m-d', $submitDate);
        if (!$checkDate) {
            throw new InvalidInputException("Date format should be Y-m-d");
        }
        try {
            DB::beginTransaction();
            $loanData = [
                'user_id' => $userId,
                'amount' => $amount,
                'term' => $term,
                'submit_date' => $submitDate,
                'status' => Loan::STATUS_PENDING
            ];
            $loan = $this->loanRepository->create($loanData);
            $repayments = $this->loanHelper->calculateRepayment($amount, $term, $submitDate);
            $repaymentItems = [];
            foreach ($repayments as $item) {
                $item['loan_id'] = $loan->id;
                $item['status'] = LoanRepayment::STATUS_PENDING;
                $repaymentItems[] = $this->loanRepaymentRepository->create($item);
            }
            DB::commit();
            $loan->repayments = $repaymentItems;
            return $loan;
        } catch (\Exception $exception) {
            DB::rollBack();
            throw $exception;
        }
    }

    public function approveLoan($loanId)
    {
        $loan = $this->loanRepository->find($loanId);
        if ($loan->status != Loan::STATUS_PENDING) {
            throw new NotAllowException("This loan is not allowed to be approved");
        }
        $updateData = ['status' => Loan::STATUS_APPROVE];
        return $this->loanRepository->update($loanId, $updateData);
    }

    public function payRepayment($repaymentId, $amount)
    {
        $repayment = $this->loanRepaymentRepository->find($repaymentId);
        if ($amount < $repayment->amount) {
            throw new NotAllowException("The payment amount is not enough");
        }
        if ($repayment->status != LoanRepayment::STATUS_PENDING) {
            throw new NotAllowException("This repayment can't not be paid");
        }
        $loan = $this->loanRepository->find($repayment->loan_id);
        $allowsPayStatus = [Loan::STATUS_APPROVE, Loan::STATUS_PARTIAL_PAID];
        if (!in_array($loan->status, $allowsPayStatus)) {
            throw new NotAllowException("This repayment can't not be paid");
        }
        try {
            DB::beginTransaction();
            $updateData = [
                'status' => LoanRepayment::STATUS_PAID,
                'paid_amount' => $amount
            ];
            $repayment = $this->loanRepaymentRepository->update($repaymentId, $updateData);
            event(new LoanRepaymentPaid($repayment));
            DB::commit();
            return $repayment;
        } catch (\Exception $exception) {
            DB::rollBack();
            throw $exception;
        }

    }
}
