<?php

namespace Tests\Feature\Services;

use App\Exceptions\InvalidInputException;
use App\Exceptions\NotAllowException;
use App\Exceptions\NotFoundException;
use App\Interfaces\Repositories\LoanRepaymentRepositoryInterface;
use App\Interfaces\Services\LoanServiceInterface;
use App\Models\Loan;
use App\Models\LoanRepayment;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Mockery\MockInterface;
use Tests\Feature\AbstractFeatureTest;

class LoanServiceTest extends AbstractFeatureTest
{
    use RefreshDatabase;

    /**
     * @var LoanServiceInterface
     */
    protected $loanService;

    protected function setUp(): void
    {
        parent::setUp();
        $this->loanService = $this->app->make(LoanServiceInterface::class);
    }

    public function test_throw_exception_with_invalid_input()
    {
        $this->expectException(InvalidInputException::class);
        $this->loanService->createLoan(1, 0, 200, '2022-12-12');
        $this->expectException(InvalidInputException::class);
        $this->loanService->createLoan(1, 5000, 0, '2022-12-12');
        $this->expectException(InvalidInputException::class);
        $this->loanService->createLoan(1, 5000, 2, 'Some randow string');
    }

    public function test_create_no_remainder_amount_loan()
    {
        $userId = 1;
        $amount = 1000;
        $term = 4;
        $loan = $this->loanService->createLoan($userId, $amount, $term);
        $this->assertTrue($loan instanceof Loan);
        $loanRepayments = $loan->repayments;
        $this->assertEquals($term, count($loanRepayments));
        $this->assertEquals(Loan::STATUS_PENDING, $loan->status);
        foreach ($loanRepayments as $repayment) {
            $this->assertTrue($repayment instanceof LoanRepayment);
            $this->assertEquals(250, $repayment->amount);
            $this->assertEquals($loan->id, $repayment->loan_id);
            $this->assertEquals(LoanRepayment::STATUS_PENDING, $repayment->status);
        }

        $totalAmount = array_reduce($loanRepayments, function ($pre, $repayment) {
            return $pre + $repayment->amount;
        }, 0);
        $this->assertEquals($amount, $totalAmount);
    }

    public function test_create_remainder_amount_loan()
    {
        $userId = 1;
        $amount = 1000;
        $term = 3;
        $startDate = '2023-04-14';
        $loan = $this->loanService->createLoan($userId, $amount, $term, $startDate);
        $this->assertTrue($loan instanceof Loan);
        $loanRepayments = $loan->repayments;
        $this->assertEquals($term, count($loanRepayments));
        $this->assertEquals(Loan::STATUS_PENDING, $loan->status);
        foreach ($loanRepayments as $repayment) {
            $this->assertTrue($repayment instanceof LoanRepayment);
            $this->assertTrue(333.33 == $repayment->amount || 333.34 == $repayment->amount);
            $this->assertEquals($loan->id, $repayment->loan_id);
            $this->assertEquals(LoanRepayment::STATUS_PENDING, $repayment->status);
        }

        $totalAmount = array_reduce($loanRepayments, function ($pre, $repayment) {
            return $pre + $repayment->amount;
        }, 0);
        $this->assertEquals($amount, $totalAmount);
    }

    public function test_revert_data_when_there_is_exception_saving_repayment()
    {
        $mockLoanRepaymentRepository = $this->mock(LoanRepaymentRepositoryInterface::class, function (MockInterface $mock) {
            $mock->shouldReceive('create')->andThrow(new \Exception('Some exception because of any reason'));
        });
        /** @var $service LoanServiceInterface */
        $service = $this->app->make(LoanServiceInterface::class, ['loanRepaymentRepository' => $mockLoanRepaymentRepository]);
        $user = User::factory()->create(['username' => 'random_user_name']);
        $this->expectException(\Exception::class);
        $service->createLoan($user->id, 500, 5, '2022-06-15');
        $this->assertDatabaseMissing('loans', ['user_id' => $user->id]);
    }

    public function test_throw_exception_when_approve_not_existed_loan()
    {
        $notExistedId = 5000;
        $this->expectException(NotFoundException::class);
        $this->loanService->approveLoan($notExistedId);
    }

    public function test_throw_exception_when_approve_non_pending_loan()
    {
        $user = User::factory()->create();
        $paidLoan = Loan::factory()->create(['user_id' => $user->id, 'status' => Loan::STATUS_PAID]);
        $approvedLoan = Loan::factory()->create(['user_id' => $user->id, 'status' => Loan::STATUS_APPROVE]);
        $this->expectException(NotAllowException::class);
        $this->loanService->approveLoan($paidLoan->id);
        $this->expectException(NotAllowException::class);
        $this->loanService->approveLoan($approvedLoan->id);
    }

    public function test_approve_loan()
    {
        $user = User::factory()->create();
        $pendingLoan = Loan::factory()->create(['user_id' => $user->id, 'status' => Loan::STATUS_PENDING]);
        $approvedLoan = $this->loanService->approveLoan($pendingLoan->id);
        $this->assertEquals(Loan::STATUS_APPROVE, $approvedLoan->status);
        $this->assertEquals($pendingLoan->id, $approvedLoan->id);
        $this->assertDatabaseHas('loans', ['id' => $pendingLoan->id, 'status' => Loan::STATUS_APPROVE]);
    }

    public function test_throw_exception_when_pay_un_exist_repayment()
    {
        $notExistedId = 5000;
        $this->expectException(NotFoundException::class);
        $this->loanService->payRepayment($notExistedId, 3000);
    }

    public function test_throw_exception_when_pay_paid_repayment()
    {
        $user = User::factory()->create();
        $loan = Loan::factory()->create(['user_id' => $user->id, 'term' => 1, 'status' => Loan::STATUS_PAID]);
        $loanRepayment = LoanRepayment::factory()->create(['loan_id' => $loan->id, 'amount' => $loan->amount, 'status' => LoanRepayment::STATUS_PAID]);
        $this->expectException(NotAllowException::class);
        $this->loanService->payRepayment($loanRepayment->id, $loanRepayment->amount);
    }

    public function test_throw_exception_when_pay_repayment_with_smaller_amount()
    {
        $user = User::factory()->create();
        $loan = Loan::factory()->create(['user_id' => $user->id, 'term' => 1, 'amount' => 1000, 'status' => Loan::STATUS_APPROVE]);
        $loanRepayment = LoanRepayment::factory()->create(['loan_id' => $loan->id, 'amount' => $loan->amount, 'status' => LoanRepayment::STATUS_PENDING]);
        $this->expectException(NotAllowException::class);
        $this->loanService->payRepayment($loanRepayment->id, 500);
    }

    public function test_throw_exception_when_pay_repayment_of_pending_or_reject_loan()
    {
        $user = User::factory()->create();
        $loan = Loan::factory()->create(['user_id' => $user->id, 'term' => 1, 'status' => Loan::STATUS_PENDING]);
        $loanRepayment = LoanRepayment::factory()->create(['loan_id' => $loan->id, 'amount' => $loan->amount, 'status' => LoanRepayment::STATUS_PENDING]);
        $this->expectException(NotAllowException::class);
        $this->loanService->payRepayment($loanRepayment->id, $loanRepayment->amount);
    }

    public function test_pay_repayment_some_repayments_successfully()
    {
        $user = User::factory()->create();
        $loan = Loan::factory()->create(['user_id' => $user->id, 'term' => 2, 'amount' => 1000, 'status' => Loan::STATUS_APPROVE]);
        $loanRepayments = LoanRepayment::factory(2)->create(['loan_id' => $loan->id, 'amount' => 500, 'status' => LoanRepayment::STATUS_PENDING]);
        $loanRepayment = $loanRepayments[0];
        $newRepayment = $this->loanService->payRepayment($loanRepayment->id, $loanRepayment->amount);
        $this->assertEquals(LoanRepayment::STATUS_PAID, $newRepayment->status);
        $this->assertEquals($loanRepayment->amount, $newRepayment->paid_amount);
        $this->assertDatabaseHas('loans', ['id' => $loan->id, 'status' => Loan::STATUS_PARTIAL_PAID]);
    }

    public function test_pay_the_last_repayment_successfully()
    {
        $user = User::factory()->create();
        $loan = Loan::factory()->create(['user_id' => $user->id, 'term' => 3, 'amount' => 3000, 'status' => Loan::STATUS_APPROVE]);
        $loanRepayments = LoanRepayment::factory(3)->create(['loan_id' => $loan->id, 'amount' => 1000, 'status' => LoanRepayment::STATUS_PENDING]);
        $firstRepayment = $loanRepayments[0];
        $newRepayment = $this->loanService->payRepayment($firstRepayment->id, $firstRepayment->amount);
        $this->assertEquals(LoanRepayment::STATUS_PAID, $newRepayment->status);
        $this->assertEquals($firstRepayment->amount, $newRepayment->paid_amount);
        $this->assertDatabaseHas('loans', ['id' => $loan->id, 'status' => Loan::STATUS_PARTIAL_PAID]);

        foreach ($loanRepayments as $loanRepayment) {
            if ($loanRepayment->id != $firstRepayment->id) {
                $newRepayment = $this->loanService->payRepayment($loanRepayment->id, $loanRepayment->amount);
                $this->assertEquals(LoanRepayment::STATUS_PAID, $newRepayment->status);
                $this->assertEquals($loanRepayment->amount, $newRepayment->paid_amount);
            }
        }
        $this->assertDatabaseHas('loans', ['id' => $loan->id, 'status' => Loan::STATUS_PAID]);
    }

    public function test_full_early_repayment_with_less_than_term_time()
    {
        $customer = $this->createCustomerUser();
        $loan = $this->generateLoan($customer->id);
        [$repayment] = $loan->repayments;
        $this->loanService->approveLoan($loan->id);
        $this->loanService->payRepayment($repayment->id, $loan->amount);
        $this->assertDatabaseHas('loans', ['id' => $loan->id, 'status' => Loan::STATUS_PAID]);
        $this->assertDatabaseHas('loan_repayments', ['loan_id' => $loan->id, 'status' => LoanRepayment::STATUS_PAID]);
        $this->assertDatabaseHas('loan_repayments', ['loan_id' => $loan->id, 'status' => LoanRepayment::STATUS_AUTO_PAID]);

    }
}
