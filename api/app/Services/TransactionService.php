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
        try {
            $senderAccount = $this->accountRepository->findById($data['sender_id'], $userId);
            $recipientAccount = $this->accountRepository->accountFindById($data['recipient_id']);

            if (!$senderAccount || !$recipientAccount) {
                throw new Exception('Invalid accounts');
            }

            if ($senderAccount->balance < $data['amount']) {
                throw new Exception('Insufficient balance');
            }

            $this->transactionRepository->createTransfer($senderAccount->id, $recipientAccount->id, $data['amount']);

            $this->accountRepository->updateBalance($senderAccount->id, -$data['amount']);
            $this->accountRepository->updateBalance($recipientAccount->id, $data['amount']);
        } catch (Exception $e) {
            throw new Exception($e->getMessage(), $e->getCode() ?: 500);
        }
    }

    /**
     * @param array $data
     * @return void
     * @throws Exception
     */
    public function deposit(array $data)
    {
        try {
            $account = $this->accountRepository->accountFindById($data['receiver_id']);

            if (!$account) {
                throw new Exception('Account not found');
            }

            $this->transactionRepository->createDeposit($account->id, $data['amount']);

            $this->accountRepository->updateBalance($account->id, $data['amount']);
        } catch (Exception $e) {
            throw new Exception($e->getMessage(), $e->getCode() ?: 500);
        }
    }
}
