<?php

namespace Database\Factories;

use App\Models\Business;
use App\Models\User;
use App\Models\Brand;
use App\Models\Customer;
use Illuminate\Database\Eloquent\Factories\Factory;

class BusinessFactory extends Factory
{
    protected $model = Business::class;

    public function definition(): array
    {
        return [
            'name' => $this->faker->company,
            'slug' => $this->faker->unique()->slug,
            'description' => $this->faker->sentence,
            'user_id' => User::factory(),
            'brand_id' => Brand::factory(),
        ];
    }

    public function configure(): BusinessFactory
    {
        return $this->afterCreating(function (Business $business) {
            Customer::factory()->count(5)->create(['business_id' => $business->id]);
        });
    }
}

