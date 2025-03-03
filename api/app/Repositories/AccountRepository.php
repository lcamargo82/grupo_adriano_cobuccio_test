<?php

namespace App\Repositories;

use App\Models\Account;

class AccountRepository
{
    /**
     * @param $userId
     * @param $authUserId
     * @return null
     */
    public function findByUserId($userId, $authUserId)
    {
        if ($userId !== $authUserId) {
            return null;
        }
        return Account::where('user_id', $userId)->first();
    }

    /**
     * @param $userId
     * @return mixed
     */
    public function findAllByUserId($userId)
    {
        return Account::where('user_id', $userId)->get();
    }

    /**
     * @param $accountId
     * @param $authUserId
     * @return mixed
     */
    public function findById($accountId, $authUserId)
    {
        return Account::where('id', $accountId)->where('user_id', $authUserId)->first();
    }

    /**
     * @param array $data
     * @return mixed
     */
    public function create(array $data)
    {
        return Account::create($data);
    }

    /**
     * @param $userId
     * @param $amount
     * @param $authUserId
     * @return null
     */
    public function updateBalance($userId, $amount, $authUserId)
    {
        if ($userId !== $authUserId) {
            return null;
        }
        $account = $this->findByUserId($userId, $authUserId);
        if ($account) {
            $account->balance += $amount;
            $account->save();
        }
        return $account;
    }

    /**
     * @param $accountId
     * @param $authUserId
     * @return bool
     */
    public function delete($accountId, $authUserId)
    {
        $account = $this->findById($accountId, $authUserId);
        if ($account && $account->balance == 0) {
            $account->delete();
            return true;
        }
        return false;
    }
}
