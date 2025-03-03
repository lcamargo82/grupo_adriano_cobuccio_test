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
     * @param $authUserId
     * @return null
     */
    public function findByTransactionId($transactionId, $authUserId)
    {
        $transaction = Transaction::where('id', $transactionId)
            ->where(function ($query) use ($authUserId) {
                $query->where('sender_id', $authUserId)
                    ->orWhere('receiver_id', $authUserId);
            })->first();

        if (!$transaction) {
            return null;
        }

        return Reversal::where('transaction_id', $transactionId)->first();
    }
}
