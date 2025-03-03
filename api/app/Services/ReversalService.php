<?php

namespace App\Services;

use App\Repositories\ReversalRepository;
use App\Repositories\TransactionRepository;
use App\Repositories\AccountRepository;
use Exception;

class ReversalService
{
    protected $reversalRepository;
    protected $transactionRepository;
    protected $accountRepository;

    /**
     * @param ReversalRepository $reversalRepository
     * @param TransactionRepository $transactionRepository
     * @param AccountRepository $accountRepository
     */
    public function __construct(ReversalRepository $reversalRepository, TransactionRepository $transactionRepository, AccountRepository $accountRepository)
    {
        $this->reversalRepository = $reversalRepository;
        $this->transactionRepository = $transactionRepository;
        $this->accountRepository = $accountRepository;
    }

    /**
     * @param $userId
     * @param array $data
     * @return void
     * @throws Exception
     */
    public function reverseTransaction($userId, array $data)
    {
        $transaction = $this->transactionRepository->findById($data['transaction_id'], $userId);

        if (!$transaction || ($transaction->sender_id !== $userId && $transaction->recipient_id !== $userId)) {
            throw new Exception('Invalid transaction or unauthorized reversal');
        }

        $senderAccount = $this->accountRepository->findByUserId($transaction->sender_id, $userId);
        $recipientAccount = $this->accountRepository->findByUserId($transaction->recipient_id, $userId);

        if ($recipientAccount->balance < $transaction->amount) {
            throw new Exception('Insufficient balance for reversal');
        }

        $this->reversalRepository->createReversal($transaction->id);
    }

    /**
     * @param $userId
     * @param array $data
     * @return void
     * @throws Exception
     */
    public function reverseDeposit($userId, array $data)
    {
        $deposit = $this->transactionRepository->findDepositById($data['deposit_id'], $userId);

        if (!$deposit || $deposit->user_id !== $userId) {
            throw new Exception('Invalid deposit or unauthorized reversal');
        }

        $account = $this->accountRepository->findByUserId($userId, $userId);

        if ($account->balance < $deposit->amount) {
            throw new Exception('Insufficient balance for reversal');
        }

        $this->reversalRepository->createReversal($deposit->id);
    }
}
