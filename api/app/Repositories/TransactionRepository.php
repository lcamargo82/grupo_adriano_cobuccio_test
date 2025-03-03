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
}
