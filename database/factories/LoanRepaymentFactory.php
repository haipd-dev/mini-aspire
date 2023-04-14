<?php

namespace Database\Factories;

use App\Models\LoanRepayment;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\LoanRepayment>
 */
class LoanRepaymentFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'amount' => fake()->randomFloat(),
            'pay_date' => fake()->date(),
            'status' => fake()->randomElement([LoanRepayment::STATUS_PENDING, LoanRepayment::STATUS_PAID]),
        ];
    }
}
