<?php

namespace Database\Factories;

use App\Models\Reversal;
use App\Models\Transaction;
use Illuminate\Database\Eloquent\Factories\Factory;

class ReversalFactory extends Factory
{
    protected $model = Reversal::class;

    public function definition()
    {
        return [
            'transaction_id' => Transaction::factory(),
            'reason' => $this->faker->sentence(),
            'deleted_at' => null
        ];
    }
}
