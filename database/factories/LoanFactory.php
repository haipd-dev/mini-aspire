<?php

namespace Database\Factories;

use App\Models\Loan;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Loan>
 */
class LoanFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'submit_date' => fake()->date('Y-m-d'),
            'status' => fake()->randomElement([Loan::STATUS_PENDING, Loan::STATUS_PAID, Loan::STATUS_APPROVE]),
            'term' => fake()->randomNumber(2),
            'amount' => fake()->randomNumber(5, true),
        ];
    }
}
