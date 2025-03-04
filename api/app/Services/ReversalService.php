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
    public function reverseTransfer($userId, array $data)
    {
        try {
            $transaction = $this->transactionRepository->findById($data['transaction_id'], $userId);

            if (!$transaction) {
                throw new Exception('Invalid transaction or unauthorized reversal');
            }

            $senderAccount = $this->accountRepository->accountFindById($transaction->sender_id);
            $recipientAccount = $this->accountRepository->accountFindById($transaction->receiver_id);

            if (!$senderAccount || !$recipientAccount) {
                throw new Exception('Accounts not found');
            }

            if ($recipientAccount->balance < $transaction->amount) {
                throw new Exception('Insufficient balance for reversal');
            }

            $this->reversalRepository->create([
                'transaction_id' => $transaction->id,
                'reason' => $data['reason'] ?? 'No reason provided'
            ]);

            $this->accountRepository->updateBalance($recipientAccount->id, -$transaction->amount);
            $this->accountRepository->updateBalance($senderAccount->id, $transaction->amount);
        } catch (Exception $e) {
            throw new Exception($e->getMessage(), $e->getCode() ?: 500);
        }
    }

    /**
     * @param array $data
     * @return void
     * @throws Exception
     */
    public function reverseDeposit(array $data)
    {
        try {
            $deposit = $this->transactionRepository->findDepositById($data['deposit_id']);

            if (!$deposit) {
                throw new Exception('Invalid deposit or unauthorized reversal');
            }

            $account = $this->accountRepository->accountFindById($deposit->receiver_id);

            if (!$account) {
                throw new Exception('Account not found');
            }

            $this->reversalRepository->create([
                'transaction_id' => $deposit->id,
                'reason' => $data['reason'] ?? 'No reason provided'
            ]);

            $this->accountRepository->updateBalance($account->id, -$deposit->amount);
        } catch (Exception $e) {
            throw new Exception($e->getMessage(), $e->getCode() ?: 500);
        }
    }
}
