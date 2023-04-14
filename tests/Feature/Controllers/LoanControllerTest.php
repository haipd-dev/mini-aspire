<?php

namespace Tests\Feature\Controllers;

use App\Models\Loan;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Response;
use Tests\TestCase;

class LoanControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_create_loan_no_token_request()
    {
        $request = $this->putJson('api/loan', []);
        $request->assertStatus(Response::HTTP_UNAUTHORIZED);
    }

    public function test_duplicate_request_create_loan()
    {

    }

    public function test_create_loan_with_invalid_input()
    {
        /** @var $user User */
        $user = User::factory()->create();
        $token = $user->createToken('Customer Token');
        $makeHttpRequest = $this->withToken($token->plainTextToken);
        $missingData = [
            [],
            [
                'request_id' => 'some_id',
                'amount' => 10000,
            ],
            [
                'request_id' => 'some_id',
                'term' => 3,
            ],
            [
                'amount' => 10000,
                'term' => 3,
            ],
            [
                'request_id' => 'some_id',
                'amount' => 10000,
                'term' => 3,
                'date' => 'some invalid date',
            ],
        ];
        foreach ($missingData as $data) {
            $response = $makeHttpRequest->putJson('api/loan', $data);
            $response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY);
        }
    }

    public function test_create_loan_successfully()
    {
        $user = User::factory()->create();
        $token = $user->createToken('Customer Token');
        $makeHttpRequest = $this->withToken($token->plainTextToken);
        $submitData = [
            'request_id' => 'some-request-id',
            'amount' => 10000,
            'term' => 3,
            'date' => '2023-04-14',
        ];
        $response = $makeHttpRequest->putJson('api/loan', $submitData);
        $response->assertStatus(Response::HTTP_CREATED)
            ->assertJsonPath('amount', 10000)
            ->assertJsonPath('submit_date', '2023-04-14')
            ->assertJsonPath('term', 3)
            ->assertJsonPath('user_id', $user->id)
            ->assertJsonPath('status', Loan::STATUS_PENDING)
            ->assertJsonPath('repayments', function ($repayments) {
                return count($repayments) == 3;
            });
        $repayments = $response->json('repayments');
        $totalAmount = array_reduce($repayments, function ($pre, $cur) {
        return $pre + $cur['amount'];
        }, 0);
        $this->assertEquals(10000, $totalAmount);
    }

    public function test_get_not_existed_loan()
    {
        $user = User::factory()->create();
        $token = $user->createToken('Customer Token');
        $client = $this->withToken($token->plainTextToken);
        $notExistedId = 5000;
        $response = $client->getJson("api/loan/$notExistedId");
        $response->assertStatus(Response::HTTP_NOT_FOUND);
    }

    public function test_get_loan_of_other_user()
    {
        $createdUser = User::factory()->create();
        $createdLoan = Loan::factory()->create(['user_id' => $createdUser->id]);
        $user = User::factory()->create();
        $token = $user->createToken('Customer Token');
        $client = $this->withToken($token->plainTextToken);
        $response = $client->getJson("api/loan/{$createdLoan->id}");
        $response->assertStatus(Response::HTTP_FORBIDDEN);
    }

    public function test_get_loan_successfully()
    {
        $user = User::factory()->create();
        $token = $user->createToken('Customer Token');
        $createdLoan = Loan::factory()->create(['user_id' => $user->id]);
        $client = $this->withToken($token->plainTextToken);
        $response = $client->getJson("api/loan/{$createdLoan->id}");
        $response->assertStatus(Response::HTTP_OK);
        $response->assertJsonPath('id', $createdLoan->id);
    }
}
