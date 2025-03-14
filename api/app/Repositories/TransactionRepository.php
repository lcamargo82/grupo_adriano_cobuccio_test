<?php

namespace App\Repositories;

use App\Models\Transaction;
use Carbon\Carbon;
use Illuminate\Support\Collection;

class TransactionRepository
{

    /**
     * @param $user
     * @param $accountId
     * @return Collection
     */
    public function getTransactionsByAccount($user, $accountId)
    {
        $transactions = Transaction::withTrashed()
            ->selectRaw('
            t.id,
            t.created_at,
            t.type,
            t.amount,
            t.sender_id,
            t.receiver_id,
            a.id as account_id,
            a.name as account_name,
            t.status
        ')
            ->from('transactions as t')
            ->join('accounts as a', function ($join) use ($accountId) {
                $join->on('t.receiver_id', '=', 'a.id')
                    ->orOn('t.sender_id', '=', 'a.id');
            })
            ->where(function ($query) use ($accountId) {
                $query->where('t.receiver_id', $accountId)
                    ->orWhere('t.sender_id', $accountId);
            })
            ->get();

        $formattedTransactions = $transactions->map(function ($transaction) use ($accountId) {

            if ($transaction->type === 'transfer') {
                if ($transaction->receiver_id === $accountId) {
                    $transaction->amount = $transaction->amount;
                    $transaction->account_name = $transaction->account_id . '-' . $transaction->account_name;
                } else {
                    $transaction->amount = -$transaction->amount;
                    $transaction->account_name = $transaction->account_id . '-' . $transaction->account_name;
                }
            } elseif ($transaction->type === 'reversal') {
                $transaction->account_name = 'Estorno';
                $transaction->amount = -$transaction->amount;
            } else {
                $transaction->account_name = 'Depósito';
            }

            unset($transaction->sender_id, $transaction->receiver_id, $transaction->account_id);

            return $transaction;
        });

        $uniqueTransactions = $formattedTransactions->unique(function ($item) {
            return $item->id . $item->amount;
        });

        return $uniqueTransactions->values();
    }

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
     * @param $accountId
     * @param $authUserId
     * @return mixed
     */
    public function findById($id, $accountId, $authUserId)
    {
        return Transaction::where('id', $id)
            ->where(function ($query) use ($authUserId, $accountId) {
                $query->where('sender_id', $accountId)
                    ->orWhere('receiver_id', $accountId);
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

    /**
     * @param $transactionId
     * @param $status
     * @return mixed
     */
    public function updateStatus($transactionId, $status)
    {
        return Transaction::where('id', $transactionId)
            ->update(['status' => $status]);
    }
}
