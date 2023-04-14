<?php

namespace App\Interfaces\Services;

interface LoanServiceInterface
{
    public function createLoan($userId, float $amount, int $term, $submitDate = null);

    public function approveLoan($loanId);

    public function payRepayment($repaymentId, $amount);
}
