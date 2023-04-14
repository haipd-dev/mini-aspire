<?php

namespace Database\Seeders;

use App\Models\Loan;
use App\Models\LoanRepayment;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class LoanSeeder extends Seeder
{
    public function run(): void
    {
        $userData = [
            'id' => 1,
            'username' => 'user1',
            'password' => Hash::make('password1'),
        ];
        $user = User::factory()->create($userData);
        $userId = $user->id;
        $data = [
            'id' => 1,
            'user_id' => $userId,
            'amount' => 10000,
            'term' => 5,
            'submit_date' => '2023-04-13',
            'status' => Loan::STATUS_APPROVE,
        ];
        Loan::query()->create($data);
        $data = [
            'id' => 2,
            'user_id' => $userId,
            'amount' => 20000,
            'term' => 2,
            'submit_date' => '2023-04-14',
            'status' => Loan::STATUS_PENDING,
        ];
        Loan::query()->create($data);
        $id = 1;
        $loan1Data = [
            'loan_id' => 1,
            'amount' => 2000,
            'status' => LoanRepayment::STATUS_PENDING,
        ];
        $loan1RepaymentDates = ['2023-04-20', '2023-04-27', '2023-05-04', '2023-05-11', '2023-05-18'];
        foreach ($loan1RepaymentDates as $date) {
            LoanRepayment::query()->create([...$loan1Data, 'id' => $id, 'pay_date' => $date]);
            $id++;
        }
        $loan2Data = [
            'loan_id' => 2,
            'amount' => 10000,
            'status' => LoanRepayment::STATUS_PENDING,
        ];
        $loan2RepaymentDates = ['2023-04-21', '2023-04-22'];
        foreach ($loan2RepaymentDates as $date) {
            LoanRepayment::query()->create([...$loan2Data, 'id' => $id, 'pay_date' => $date]);
            $id++;
        }
    }
}
