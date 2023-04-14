<?php

namespace Tests\Unit;

use App\Exceptions\InvalidInputException;
use App\Helpers\LoanHelper;
use PHPUnit\Framework\TestCase;

class LoanHelperTest extends TestCase
{
    public function test_throw_exception_on_invalid_input()
    {
        $loanHelper = new LoanHelper();
        $date = '2023-04-14';
        $this->expectException(InvalidInputException::class);
        $loanHelper->calculateRepayment(-1, 2, $date);
        $this->expectException(InvalidInputException::class);
        $loanHelper->calculateRepayment(3000, 0, $date);
        $this->expectException(InvalidInputException::class);
        $loanHelper->calculateRepayment(-1, -1, $date);
        $this->expectException(InvalidInputException::class);
        $loanHelper->calculateRepayment(500, 2, 'Some random string');
    }

    public function test_generate_amount_which_no_remainder_amount(): void
    {
        $amount = 3000;
        $term = 3;
        $expectRepaymentAmount = 1000;
        $date = '2023-04-14';
        $expectedDates = ['2023-04-21', '2023-04-28', '2023-05-05'];
        $this->test_data($amount, $term, $date, $expectRepaymentAmount, $expectedDates);
    }

    public function test_generate_amount_which_remainder_amount(): void
    {
        $amount = 4000;
        $term = 3;
        $expectRepaymentAmounts = [1333.33, 1333.33, 1333.34];
        $date = '2023-04-13';
        $expectedDates = ['2023-04-20', '2023-04-27', '2023-05-04'];
        $this->test_data($amount, $term, $date, $expectRepaymentAmounts, $expectedDates);
    }

    private function test_data($amount, $term, $date, $expectedAmounts, $expectedDates)
    {
        $loanHelper = new LoanHelper();
        $repaymentsData = $loanHelper->calculateRepayment($amount, $term, $date);
        $this->assertIsArray($repaymentsData);
        $calculatedAmount = 0;
        foreach ($repaymentsData as $index => $repayment) {
            $calculatedAmount += $repayment['amount'];
            if (is_array($expectedAmounts)) {
                $this->assertEquals($expectedAmounts[$index], $repayment['amount']);
            } else {
                $this->assertEquals($expectedAmounts, $repayment['amount']);
            }
            $this->assertEquals($expectedDates[$index], $repayment['pay_date']);
        }
        $this->assertEquals($amount, $calculatedAmount);
    }
}
