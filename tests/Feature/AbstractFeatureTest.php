<?php

namespace Tests\Feature;

use App\Interfaces\Services\LoanServiceInterface;
use App\Models\Loan;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AbstractFeatureTest extends TestCase
{
    use RefreshDatabase;

    /**
     * @return User
     */
    protected function createAdminUser()
    {
        return User::factory()->create(['user_type' => User::TYPE_ADMIN]);
    }

    /**
     * @return User
     */
    protected function createCustomerUser()
    {
        return User::factory()->create(['user_type' => User::TYPE_CUSTOMER]);
    }

    /**
     * @return Loan
     *
     * @throws \App\Exceptions\InvalidInputException
     */
    protected function generateLoan($userId)
    {
        /** @var $loanService LoanService */
        $loanService = $this->app->make(LoanServiceInterface::class);
        $amount = fake()->randomElement([1000, 2000, 3000, 4000, 5000]);
        $term = fake()->randomElement([2, 3, 4, 5, 6]);

        return $loanService->createLoan($userId, $amount, $term);
    }
}
