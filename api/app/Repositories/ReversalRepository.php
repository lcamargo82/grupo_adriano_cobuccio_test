<?php

namespace App\Repositories;

use App\Models\Reversal;
use App\Models\Transaction;

class ReversalRepository
{
    /**
     * @param array $data
     * @return mixed
     */
    public function create(array $data)
    {
        return Reversal::create($data);
    }

    /**
     * @param $transactionId
     * @return mixed
     */
    public function findByTransactionId($transactionId)
    {
        return Reversal::where('transaction_id', $transactionId)->first();
    }

    /**
     * @param $transactionId
     * @return mixed
     */
    public function transactionExists($transactionId)
    {
        return Transaction::where('id', $transactionId)->exists();
    }
}
