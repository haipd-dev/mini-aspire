<?php

namespace Tests\Feature\Controllers;
use App\Interfaces\Services\LoanServiceInterface;
use App\Models\Loan;
use App\Models\LoanRepayment;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Response;
use Tests\Feature\AbstractFeatureTest;

class LoanRepaymentControllerTest extends AbstractFeatureTest
{
    /**
     * @var LoanServiceInterface
     */
    protected $loanService;
    protected function setUp(): void
    {
        parent::setUp();
        $this->loanService = $this->app->make(LoanServiceInterface::class);
    }

    public function test_unauthorized_when_pay_with_unauthorized_token(){
        $randomId = 2;
        $response = $this->postJson("api/loan-repayment/$randomId/pay", []);
        $response->assertStatus(Response::HTTP_UNAUTHORIZED);
        $response = $this->withToken("Some random token")->postJson("api/loan-repayment/$randomId/pay", []);
        $response->assertStatus(Response::HTTP_UNAUTHORIZED);
    }

    public function test_validate_amount_format_on_pay(){
        $customerUser = $this->createCustomerUser();
        $token = $customerUser->createToken("User Token");
        $loan = $this->generateLoan($customerUser->id);
        [$repayment] = $loan->repayments;
        $response = $this->withToken($token->plainTextToken)->postJson("api/loan-repayment/{$repayment->id}/pay", []);
        $response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY);
        $response->assertInvalid(['amount']);
        $response = $this->withToken($token->plainTextToken)->postJson("api/loan-repayment/{$repayment->id}/pay", [ 'amount' => "Some random amount"]);
        $response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY);
        $response->assertInvalid(['amount']);
    }

    public function test_reject_pay_repayment_of_other_user(){
        $customerUser = $this->createCustomerUser();
        $loan = $this->generateLoan($customerUser->id);
        $this->loanService->approveLoan($loan->id);
        $otherCustomer = $this->createCustomerUser();
        $token = $otherCustomer->createToken("User token");
        [$repayment] = $loan->repayments;
        $response = $this->withToken($token->plainTextToken)->postJson("api/loan-repayment/{$repayment->id}/pay", ['amount' => $repayment->amount]);
        $response->assertStatus(Response::HTTP_FORBIDDEN);

    }

    public function test_prevent_pay_because_of_loan_status(){
        $customerUser = $this->createCustomerUser();
        $loan = $this->generateLoan($customerUser->id);
        $token = $customerUser->createToken("User token");
        [$repayment] = $loan->repayments;
        $response = $this->withToken($token->plainTextToken)->postJson("api/loan-repayment/{$repayment->id}/pay", ['amount' => $repayment->amount]);
        $response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY);
    }

    public function test_prevent_pay_with_smaller_amount(){
        $customerUser = $this->createCustomerUser();
        $loan = $this->generateLoan($customerUser->id);
        $this->loanService->approveLoan($loan->id);
        $token = $customerUser->createToken("User token");
        [$repayment] = $loan->repayments;
        $response = $this->withToken($token->plainTextToken)->postJson("api/loan-repayment/{$repayment->id}/pay", ['amount' => 10]);
        $response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY);
    }

    public function test_pay_with_greater_amount(){
        $customerUser = $this->createCustomerUser();
        $loan = $this->generateLoan($customerUser->id);
        $this->loanService->approveLoan($loan->id);
        $token = $customerUser->createToken("User token");
        [$repayment] = $loan->repayments;
        $amount = $repayment->amount + 10;
        $response = $this->withToken($token->plainTextToken)->postJson("api/loan-repayment/{$repayment->id}/pay", ['amount' => $amount]);
        $response->assertStatus(Response::HTTP_OK);
        $response->assertJsonPath('id', $repayment->id);
        $response->assertJsonPath('status', LoanRepayment::STATUS_PAID);
        $response->assertJsonPath('paid_amount', function ($result) use ($amount) {return (double) $result == (double) $amount ;});
        $this->assertDatabaseHas('loans', ['id' => $loan->id, 'status' => Loan::STATUS_PARTIAL_PAID]);
    }

    public function test_pay_successfully_and_change_loan_to_partial_paid(){
        $customerUser = $this->createCustomerUser();
        $loan = $this->generateLoan($customerUser->id);
        $this->loanService->approveLoan($loan->id);
        $token = $customerUser->createToken("User token");
        [$repayment] = $loan->repayments;
        $response = $this->withToken($token->plainTextToken)->postJson("api/loan-repayment/{$repayment->id}/pay", ['amount' => $repayment->amount]);
        $response->assertStatus(Response::HTTP_OK);
        $response->assertJsonPath('id', $repayment->id);
        $response->assertJsonPath('status', LoanRepayment::STATUS_PAID);
        $response->assertJsonPath('paid_amount', function ($amount) use ($repayment) {return (double) $amount == (double) $repayment->amount ;});
        $this->assertDatabaseHas('loans', ['id' => $loan->id, 'status' => Loan::STATUS_PARTIAL_PAID]);
    }

    public function test_pay_all_repayments_successfully_and_change_loan_to_paid(){
        $customerUser = $this->createCustomerUser();
        $loan = $this->generateLoan($customerUser->id);
        $this->loanService->approveLoan($loan->id);
        $token = $customerUser->createToken("User token");
        $repayments = $loan->repayments;
        foreach ($repayments as $repayment){
            $response = $this->withToken($token->plainTextToken)->postJson("api/loan-repayment/{$repayment->id}/pay", ['amount' => $repayment->amount]);
            $response->assertStatus(Response::HTTP_OK);
            $response->assertJsonPath('id', $repayment->id);
            $response->assertJsonPath('paid_amount', function ($amount) use ($repayment) {return (double) $amount == (double) $repayment->amount ;});
            $response->assertJsonPath('status', LoanRepayment::STATUS_PAID);
        }
        $this->assertDatabaseHas('loans', ['id' => $loan->id, 'status' => Loan::STATUS_PAID]);
    }

}
