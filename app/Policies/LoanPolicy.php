<?php

namespace App\Policies;

use App\Models\Loan;
use App\Models\User;

class LoanPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return $user->user_type == User::TYPE_ADMIN;
    }

    public function approve(User $user)
    {
        return $user->user_type == User::TYPE_ADMIN;
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, Loan $loan): bool
    {
        return $user->user_type == User::TYPE_ADMIN || $user->id == $loan->user_id;
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return $user->user_type == User::TYPE_CUSTOMER;
    }
}
