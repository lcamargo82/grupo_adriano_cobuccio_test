<?php

namespace App\Services;

use App\Repositories\AccountRepository;
use Exception;

class AccountService
{
    protected $accountRepository;

    /**
     * @param AccountRepository $accountRepository
     */
    public function __construct(AccountRepository $accountRepository)
    {
        $this->accountRepository = $accountRepository;
    }

    /**
     * @param $userId
     * @return mixed
     */
    public function getAll($userId)
    {
        try {
            return $this->accountRepository->findAllByUserId($userId);
        } catch (Exception $e) {
            throw new Exception($e->getMessage(), $e->getCode() ?: 500);
        }
    }

    /**
     * @param $userId
     * @param $accountId
     * @return mixed
     */
    public function getById($userId, $accountId)
    {
        try {
            $account = $this->accountRepository->findById($accountId, $userId);

            if (!$account) {
                throw new Exception('Account not found', 404);
            }

            return $account;
        } catch (Exception $e) {
            throw new Exception($e->getMessage(), $e->getCode() ?: 500);
        }
    }

    /**
     * @param $userId
     * @param array $data
     * @return mixed
     */
    public function create($userId, array $data)
    {
        try {
            return $this->accountRepository->create(['user_id' => $userId, 'name' => $data['name']]);
        } catch (Exception $e) {
            dd($e);
            throw new Exception($e->getMessage(), $e->getCode() ?: 500);
        }
    }

    /**
     * @param $userId
     * @param $accountId
     * @return bool
     * @throws Exception
     */
    public function delete($userId, $accountId)
    {
        try {
            $account = $this->accountRepository->findById($accountId, $userId);

            if (!$account) {
                throw new Exception('Account not found');
            }

            if ($account->balance != 0) {
                throw new Exception('Account must have zero balance to be deleted');
            }

            return $this->accountRepository->delete($accountId, $userId);
        } catch (Exception $e) {
            throw new Exception($e->getMessage(), $e->getCode() ?: 500);
        }
    }
}
