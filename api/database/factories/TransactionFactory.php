<?php

namespace Database\Factories;

use App\Models\Account;
use App\Models\Transaction;
use Illuminate\Database\Eloquent\Factories\Factory;

class TransactionFactory extends Factory
{
    protected $model = Transaction::class;

    public function definition()
    {
        return [
            'sender_id' => Account::factory(),
            'receiver_id' => Account::factory(),
            'amount' => $this->faker->randomFloat(2, 1, 500),
            'type' => $this->faker->randomElement(['deposit', 'transfer']),
            'deleted_at' => null
        ];
    }
}
