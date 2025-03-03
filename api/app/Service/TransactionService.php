<?php

namespace App\Services;

use App\Models\Transaction;
use App\Models\Account;
use Illuminate\Support\Facades\DB;
use Exception;

class TransactionService
{
    public function transfer($senderId, $receiverId, $amount)
    {
        DB::beginTransaction();
        try {
            $senderAccount = Account::where('user_id', $senderId)->first();
            $receiverAccount = Account::where('user_id', $receiverId)->first();

            if ($senderAccount->balance < $amount) {
                throw new Exception('Saldo insuficiente');
            }

            $senderAccount->balance -= $amount;
            $senderAccount->save();

            $receiverAccount->balance += $amount;
            $receiverAccount->save();

            Transaction::create([
                'sender_id' => $senderId,
                'receiver_id' => $receiverId,
                'amount' => $amount,
                'type' => 'transfer'
            ]);

            DB::commit();
            return true;
        } catch (Exception $e) {
            DB::rollback();
            throw $e;
        }
    }
}
