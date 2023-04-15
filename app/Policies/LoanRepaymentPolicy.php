<?php

namespace App\Policies;

use App\Models\LoanRepayment;
use App\Models\User;

class LoanRepaymentPolicy
{
    public function pay(User $user, LoanRepayment $loanRepayment)
    {
        return $user->id == $loanRepayment->loan->user_id;
    }
}
