<?php

namespace Tests\Feature\Repositories;

use App\Exceptions\NotFoundException;
use App\Interfaces\Repositories\LoanRepaymentRepositoryInterface;
use App\Interfaces\Repositories\LoanRepositoryInterface;
use App\Models\Loan;
use App\Models\LoanRepayment;
use Database\Seeders\LoanSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
class LoanPaymentRepositoryTest extends TestCase
{
    use RefreshDatabase;

    /**
     * @var LoanRepaymentRepositoryInterface
     */
    protected $repository;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(LoanSeeder::class);
        $this->repository = $this->app->make(LoanRepaymentRepositoryInterface::class);
    }

    public function test_get_model(){
        $model = $this->repository->getModel();
        $this->assertEquals(LoanRepayment::class, $model);
    }
    public function test_find_loan_repayment(): void
    {
        /** @var  $repository LoanRepositoryInterface */
        $record = $this->repository->find(1);
        $this->assertTrue($record instanceof LoanRepayment);
        $this->assertEquals(1, $record->id);
    }
//
    public function test_not_found_loan_repayment()
    {
        $this->expectException(NotFoundException::class);
        $this->repository->find(200);
    }

    public function test_create_loan_repayment()
    {
        $payDate = "2023-04-13";
        $loanId = 1;
        $amount = 200;
        $status = LoanRepayment::STATUS_PENDING;
        $data = [
            'loan_id' => $loanId,
            'amount' => $amount,
            'status' => $status,
            'pay_date' => $payDate
        ];

        $model = $this->repository->create($data);
        $this->assertTrue($model instanceof LoanRepayment);
        $this->assertEquals($loanId, $model->loan_id);
        $this->assertEquals($amount, $model->amount);
        $this->assertEquals($payDate, $model->pay_date);
        $this->assertEquals($status, $model->status);
    }

    public function test_get_all()
    {
        $allRecords = count($this->repository->getAll());
        $this->assertDatabaseCount('loan_repayments', $allRecords);
    }

    public function test_update_exited_record()
    {
        $id = 1;
        $status = LoanRepayment::STATUS_PAID;
        $amount = 123;
        $payDate = '2022-04-29';
        $updateData = [
            'status' => $status,
            'amount' => $amount,
            'pay_date' => $payDate,
        ];
        $model = $this->repository->update($id, $updateData);
        $this->assertEquals($status, $model->status);
        $this->assertEquals($amount, $model->amount);
        $this->assertEquals($payDate, $model->pay_date);
    }

    public function test_update_not_existed_record()
    {
        $loanRepaymentId = 1200;
        $status = LoanRepayment::STATUS_PAID;
        $amount = 123;
        $payDate = '2022-04-29';
        $updateData = [
            'status' => $status,
            'amount' => $amount,
            'pay_date' => $payDate,
        ];
        $this->expectException(NotFoundException::class);
        $this->repository->update($loanRepaymentId, $updateData);
    }
    public function test_delete_record(){
        $loanRepaymentId = 1;
        $result = $this->repository->delete($loanRepaymentId);
        $this->assertTrue($result);
        $this->assertDatabaseMissing('loan_repayments',['id' => $loanRepaymentId]);
        $notExistedRecordId = 5000;
        $result = $this->repository->delete($notExistedRecordId);
        $this->assertDatabaseMissing('loan_repayments',['id' => $notExistedRecordId]);
        $this->assertFalse($result);
    }
}
