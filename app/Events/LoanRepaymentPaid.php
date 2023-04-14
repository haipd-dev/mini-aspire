<?php

namespace App\Events;

use App\Models\LoanRepayment;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class LoanRepaymentPaid
{
    use Dispatchable, SerializesModels;

    protected $repayment;

    /**
     * Create a new event instance.
     */
    public function __construct(LoanRepayment $loanRepayment)
    {
        $this->repayment = $loanRepayment;
    }

    public function getLoanRepayment()
    {
        return $this->repayment;
    }
}
