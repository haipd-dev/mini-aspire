<?php

namespace Tests\Feature\Repositories;

use App\Exceptions\NotFoundException;
use App\Interfaces\Repositories\LoanRepositoryInterface;
use App\Models\Loan;
use Database\Seeders\LoanSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class LoanRepositoryTest extends TestCase
{
    use RefreshDatabase;

    /**
     * @var LoanRepositoryInterface
     */
    protected $repository;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(LoanSeeder::class);
        $this->repository = $this->app->make(LoanRepositoryInterface::class);
    }


    public function test_get_model()
    {
        $model = $this->repository->getModel();
        $this->assertEquals(Loan::class, $model);
    }

    /**
     * A basic feature test example.
     */
    public function test_find_loan(): void
    {
        /** @var  $repository LoanRepositoryInterface */
        $user = $this->repository->find(1);
        $this->assertTrue($user instanceof Loan);
        $this->assertEquals(1, $user->id);
    }

    public function test_not_found_loan()
    {
        $this->expectException(NotFoundException::class);
        $this->repository->find(3);
    }

    public function test_create_loan()
    {

        $userId = 1;
        $amount = 3000;
        $term = 3;
        $submitDate = "2023-04-13";
        $status = Loan::STATUS_PENDING;

        $data = [
            'user_id' => $userId,
            'amount' => $amount,
            'term' => $term,
            'submit_date' => $submitDate,
            'status' => $status
        ];
        $model = $this->repository->create($data);
        $this->assertTrue($model instanceof Loan);
        $this->assertEquals($userId, $model->user_id);
        $this->assertEquals($amount, $model->amount);
        $this->assertEquals($term, $model->term);
        $this->assertEquals($submitDate, $model->submit_date);
        $this->assertEquals($status, $model->status);
    }

    public function test_get_all()
    {
        $allRecords = count($this->repository->getAll());
        $this->assertDatabaseCount('loans', $allRecords);
    }

    public function test_update_exited_record()
    {
        $id = 1;
        $status = Loan::STATUS_PAID;
        $amount = 123;
        $term = 4;
        $submitDate = '2022-03-29';
        $updateData = [
            'status' => $status,
            'amount' => $amount,
            'term' => $term,
            'submit_date' => $submitDate
        ];
        $model = $this->repository->update($id, $updateData);
        $this->assertEquals($status, $model->status);
        $this->assertEquals($amount, $model->amount);
        $this->assertEquals($term, $model->term);
        $this->assertEquals($submitDate, $model->submit_date);
    }

    public function test_update_not_existed_record()
    {
        $loanId = 1200;
        $status = Loan::STATUS_PAID;
        $amount = 123;
        $term = 4;
        $submitDate = '2022-03-29';
        $updateData = [
            'status' => $status,
            'amount' => $amount,
            'term' => $term,
            'submit_date' => $submitDate
        ];
        $this->expectException(NotFoundException::class);
        $this->repository->update($loanId, $updateData);
    }

    public function test_delete_record()
    {
        $loanId = 1;
        $result = $this->repository->delete($loanId);
        $this->assertDatabaseMissing('loans', ['id' => $loanId]);
        $this->assertTrue($result);
        $notExistedRecordId = 5000;
        $result = $this->repository->delete($notExistedRecordId);
        $this->assertFalse($result);
        $this->assertDatabaseMissing('loans', ['id' => $notExistedRecordId]);

    }
}
