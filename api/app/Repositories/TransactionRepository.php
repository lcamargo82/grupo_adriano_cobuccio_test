<?php

namespace App\Repositories;

use App\Models\Transaction;

class TransactionRepository
{
    /**
     * @param array $data
     * @return mixed
     */
    public function create(array $data)
    {
        return Transaction::create($data);
    }

    /**
     * @param $id
     * @param $authUserId
     * @return mixed
     */
    public function findById($id, $authUserId)
    {
        return Transaction::where('id', $id)
            ->where(function ($query) use ($authUserId) {
                $query->where('sender_id', $authUserId)
                    ->orWhere('receiver_id', $authUserId);
            })
            ->first();
    }

    /**
     * @param $senderAccountId
     * @param $recipientAccountId
     * @param $amount
     * @return mixed
     */
    public function createTransfer($senderAccountId, $recipientAccountId, $amount)
    {
        return Transaction::create([
            'sender_id' => $senderAccountId,
            'receiver_id' => $recipientAccountId,
            'amount' => $amount,
            'type' => 'transfer'
        ]);
    }

    /**
     * Create a deposit transaction.
     *
     * @param int $accountId
     * @param float $amount
     * @return Transaction
     */
    public function createDeposit($accountId, $amount)
    {
        return Transaction::create([
            'receiver_id' => $accountId,
            'amount' => $amount,
            'type' => 'deposit'
        ]);
    }

    /**
     * Get all transactions related to a specific account.
     *
     * @param int $accountId
     * @return mixed
     */
    public function findByAccountId($accountId)
    {
        return Transaction::where('sender_id', $accountId)
            ->orWhere('receiver_id', $accountId)
            ->get();
    }

    /**
     * @param $depositId
     * @return mixed
     */
    public function findDepositById($depositId)
    {
        return Transaction::where('id', $depositId)->where('type', 'deposit')->first();
    }
}
