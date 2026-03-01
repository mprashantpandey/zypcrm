<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\SubscriptionPlan;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User::factory(10)->create();

        User::factory()->create([
            'name' => 'Super Admin',
            'email' => 'admin@librarysaas.com',
            'password' => Hash::make('password'),
            'role' => 'super_admin',
        ]);

        SubscriptionPlan::create([
            'name' => 'Starter Plan',
            'description' => 'Perfect for small libraries.',
            'price' => 29.99,
            'billing_cycle' => 'monthly',
            'max_students' => 50,
            'is_active' => true,
        ]);
    }
}