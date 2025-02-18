<?php

namespace Database\Factories;

use App\Models\Customer;
use App\Models\Business;
use App\Models\PointTransaction;
use Illuminate\Database\Eloquent\Factories\Factory;

class CustomerFactory extends Factory
{
    protected $model = Customer::class;

    public function definition(): array
    {
        return [
            'first_name' => $this->faker->firstName,
            'last_name' => $this->faker->lastName,
            'email' => $this->faker->unique()->safeEmail,
            'phone_number' => $this->faker->phoneNumber,
            'description' => $this->faker->sentence,
            'point_balance' => $this->faker->numberBetween(0, 1000),
            'business_id' => Business::factory(),
        ];
    }

    public function configure(): CustomerFactory
    {
        return $this->afterCreating(function (Customer $customer) {
            PointTransaction::factory()->count(10)->create(['customer_id' => $customer->id]);
        });
    }
}

