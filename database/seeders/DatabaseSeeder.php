<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Brand;
use App\Models\Business;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // Create a test user
        $testUser = User::factory()->create([
            'name'  => 'Test User',
            'email' => 'test@example.com',
        ]);

        $brands = Brand::factory()->count(3)->create();

        $businesses = Business::factory()->count(5)->create([
            'user_id' => $testUser->id,
            // Optionally, if you want to link a business to one of the brands we created, set its brand_id:
            // 'brand_id' => $brands->random()->id,
        ]);

        // Note: Each Business instance created using the BusinessFactory will automatically generate
        // its related Customer instances via the afterCreating callback, and each Customer will create
        // multiple PointTransaction entries as defined in its factory.
    }
}
