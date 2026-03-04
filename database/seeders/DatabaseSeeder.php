<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Wallet;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    public function run(): void
    {
        // 1. Create a specific test user
        $testUser = User::factory()->create([
            'username' => 'testuser',
            'email' => 'test@example.com',
            'phone' => '081234567890',
            'password' => bcrypt('password'), // password is 'password'
        ]);

        Wallet::create([
            'user_id' => $testUser->id,
            'balance' => 1000000, // Give them 1,000,000 initial balance
        ]);

        // 2. Create 10 dummy users
        $dummyUsers = User::factory(10)->create();

        // 3. Create wallets for dummy users with random balances
        foreach ($dummyUsers as $user) {
            Wallet::create([
                'user_id' => $user->id,
                'balance' => rand(100, 1000) * 1000, // random balance between 100,000 and 1,000,000
            ]);
        }
    }
}
