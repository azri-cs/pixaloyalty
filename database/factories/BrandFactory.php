<?php

namespace Database\Factories;

use App\Models\Brand;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class BrandFactory extends Factory
{
    protected $model = Brand::class;

    public function definition(): array
    {
        return [
            'name' => $this->faker->unique()->word,
            'slug' => $this->faker->unique()->slug,
            'description' => $this->faker->sentence,
        ];
    }

    public function configure(): BrandFactory
    {
        return $this->afterCreating(function (Brand $brand) {
            $users = User::factory()->count(3)->create();
            $brand->users()->attach($users);
        });
    }
}
