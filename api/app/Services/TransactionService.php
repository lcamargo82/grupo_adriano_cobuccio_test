<?php

namespace App\Services;

use App\Repositories\TransactionRepository;
use App\Repositories\AccountRepository;
use Exception;

class TransactionService
{
    protected $transactionRepository;
    protected $accountRepository;

    /**
     * @param TransactionRepository $transactionRepository
     * @param AccountRepository $accountRepository
     */
    public function __construct(TransactionRepository $transactionRepository, AccountRepository $accountRepository)
    {
        $this->transactionRepository = $transactionRepository;
        $this->accountRepository = $accountRepository;
    }

    /**
     * @param $userId
     * @param array $data
     * @return void
     * @throws Exception
     */
    public function transfer($userId, array $data)
    {
        $senderAccount = $this->accountRepository->findByUserId($userId, $userId);
        $recipientAccount = $this->accountRepository->findByUserId($data['recipient_id'], $userId);

        if (!$senderAccount || !$recipientAccount) {
            throw new Exception('Invalid accounts');
        }

        if ($senderAccount->balance < $data['amount']) {
            throw new Exception('Insufficient balance');
        }

        if ($userId !== $senderAccount->user_id) {
            throw new Exception('Unauthorized transaction');
        }

        $this->transactionRepository->createTransfer($senderAccount->id, $recipientAccount->id, $data['amount']);
    }

    /**
     * @param $userId
     * @param array $data
     * @return void
     * @throws Exception
     */
    public function deposit($userId, array $data)
    {
        $account = $this->accountRepository->findByUserId($userId, $userId);

        if (!$account) {
            throw new Exception('Account not found');
        }

        if ($userId !== $account->user_id) {
            throw new Exception('Unauthorized deposit');
        }

        $this->transactionRepository->createDeposit($account->id, $data['amount']);
    }
}
