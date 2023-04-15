<?php

namespace App\Policies;

use App\Models\LoanRepayment;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class LoanRepaymentPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return $user->user_type == User::TYPE_ADMIN;
    }

    public function pay(User $user, LoanRepayment $loanRepayment){
        return $user->id == $loanRepayment->loan->user_id;
    }
}
