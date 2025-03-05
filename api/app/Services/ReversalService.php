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

    public function __construct(ReversalRepository $reversalRepository, TransactionRepository $transactionRepository, AccountRepository $accountRepository)
    {
        $this->reversalRepository = $reversalRepository;
        $this->transactionRepository = $transactionRepository;
        $this->accountRepository = $accountRepository;
    }

    public function reverseTransfer($userId, array $data)
    {
        try {
            $transaction = $this->transactionRepository->findById($data['transaction_id'], $data['account_id'], $userId);

            if (!$transaction) {
                throw new Exception('Invalid transaction or unauthorized reversal');
            }

            $senderAccount = $this->accountRepository->accountFindById($transaction->sender_id);
            $recipientAccount = $this->accountRepository->accountFindById($transaction->receiver_id);

            if (!$senderAccount || !$recipientAccount) {
                throw new Exception('Accounts not found');
            }

            if ($senderAccount->user_id !== $userId) {
                throw new Exception('Unauthorized user');
            }

            if ($transaction->status === false) {
                throw new Exception('Transaction already reversed');
            }


            $this->reversalRepository->create([
                'transaction_id' => $transaction->id,
                'reason' => $data['reason'] ?? 'No reason provided',
                'type' => 'reversal',
            ]);

            $this->transactionRepository->create([
                'sender_id' => $transaction->receiver_id,
                'receiver_id' => $transaction->sender_id,
                'amount' => $transaction->amount,
                'type' => 'reversal',
                'description' => 'Reversão de Transferência',
                'status' => false,
            ]);

            $this->transactionRepository->updateStatus($transaction->id, false);

            $this->accountRepository->updateBalance($recipientAccount->id, -$transaction->amount);
            $this->accountRepository->updateBalance($senderAccount->id, $transaction->amount);
        } catch (Exception $e) {
            throw new Exception($e->getMessage(), $e->getCode() ?: 500);
        }
    }

    public function reverseDeposit($userId, array $data)
    {
        try {
            $deposit = $this->transactionRepository->findDepositById($data['transaction_id']);

            if (!$deposit) {
                throw new Exception('Invalid deposit or unauthorized reversal');
            }

            $account = $this->accountRepository->accountFindById($deposit->receiver_id);

            if (!$account) {
                throw new Exception('Account not found');
            }

            if ($account->user_id !== $userId) {
                throw new Exception('Unauthorized user');
            }

            if ($deposit->status === false) {
                throw new Exception('Deposit already reversed');
            }

            $account = $this->accountRepository->accountFindById($deposit->receiver_id);

            if (!$account) {
                throw new Exception('Account not found');
            }


            $this->reversalRepository->create([
                'transaction_id' => $deposit->id,
                'reason' => $data['reason'] ?? 'No reason provided',
                'type' => 'reversal',
            ]);

            $this->transactionRepository->create([
                'sender_id' => $deposit->receiver_id,
                'receiver_id' => $deposit->sender_id,
                'amount' => -$deposit->amount,
                'type' => 'reversal',
                'description' => 'Reversão de Depósito',
                'status' => false,
            ]);

            $this->transactionRepository->updateStatus($deposit->id, false);

            $this->accountRepository->updateBalance($account->id, -$deposit->amount);
        } catch (Exception $e) {
            throw new Exception($e->getMessage(), $e->getCode() ?: 500);
        }
    }
}
