<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use App\Models\Account;
use App\Models\Transaction;
use App\Models\User;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        User::factory(10)->create()->each(function ($user) {
            $account = Account::factory()->for($user)->create();

            Transaction::factory(5)->create([
                'sender_id' => $user->id,
                'receiver_id' => User::inRandomOrder()->first()->id,
            ]);
        });
    }
}
