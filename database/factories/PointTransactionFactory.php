<?php

namespace Database\Factories;

use App\Models\PointTransaction;
use App\Models\Customer;
use Illuminate\Database\Eloquent\Factories\Factory;

class PointTransactionFactory extends Factory
{
    protected $model = PointTransaction::class;

    public function definition(): array
    {
        return [
            'transaction_type' => $this->faker->randomElement(['issue', 'deduct']),
            'amount' => $this->faker->numberBetween(1, 100),
            'description' => $this->faker->sentence,
            'customer_id' => Customer::factory(),
        ];
    }
}

