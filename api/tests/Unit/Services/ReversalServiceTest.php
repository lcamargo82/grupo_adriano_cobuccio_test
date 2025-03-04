<?php

namespace Tests\Unit\Services;

use Tests\TestCase;
use App\Services\ReversalService;
use App\Repositories\ReversalRepository;
use App\Repositories\TransactionRepository;
use App\Repositories\AccountRepository;
use Mockery;
use Exception;

class ReversalServiceTest extends TestCase
{
    protected $reversalService;
    protected $reversalRepository;
    protected $transactionRepository;
    protected $accountRepository;

    protected function setUp(): void
    {
        parent::setUp();

        $this->reversalRepository = Mockery::mock(ReversalRepository::class);
        $this->transactionRepository = Mockery::mock(TransactionRepository::class);
        $this->accountRepository = Mockery::mock(AccountRepository::class);

        $this->reversalService = new ReversalService(
            $this->reversalRepository,
            $this->transactionRepository,
            $this->accountRepository
        );
    }

    public function test_reverse_transfer_success()
    {
        $userId = 1;
        $transactionId = 100;
        $data = ['transaction_id' => $transactionId, 'reason' => 'Fraud detected'];

        $transaction = (object) ['id' => $transactionId, 'sender_id' => 1, 'receiver_id' => 2, 'amount' => 500];
        $senderAccount = (object) ['id' => 1, 'balance' => 1000];
        $recipientAccount = (object) ['id' => 2, 'balance' => 600];

        $this->transactionRepository->shouldReceive('findById')
            ->with($transactionId, $userId)
            ->andReturn($transaction);

        $this->accountRepository->shouldReceive('accountFindById')
            ->with(1)
            ->andReturn($senderAccount);

        $this->accountRepository->shouldReceive('accountFindById')
            ->with(2)
            ->andReturn($recipientAccount);

        $this->reversalRepository->shouldReceive('create')->once();
        $this->accountRepository->shouldReceive('updateBalance')->with(2, -500)->once();
        $this->accountRepository->shouldReceive('updateBalance')->with(1, 500)->once();

        $this->reversalService->reverseTransfer($userId, $data);
        $this->assertTrue(true);
    }

    public function test_reverse_transfer_transaction_not_found()
    {
        $userId = 1;
        $data = ['transaction_id' => 999];

        $this->transactionRepository->shouldReceive('findById')
            ->with(999, $userId)
            ->andReturn(null);

        $this->expectException(Exception::class);
        $this->expectExceptionMessage('Invalid transaction or unauthorized reversal');

        $this->reversalService->reverseTransfer($userId, $data);
    }

    public function test_reverse_transfer_account_not_found()
    {
        $userId = 1;
        $transactionId = 100;
        $data = ['transaction_id' => $transactionId];

        $transaction = (object) ['id' => $transactionId, 'sender_id' => 1, 'receiver_id' => 2, 'amount' => 500];

        $this->transactionRepository->shouldReceive('findById')
            ->with($transactionId, $userId)
            ->andReturn($transaction);

        $this->accountRepository->shouldReceive('accountFindById')
            ->with(1)
            ->andReturn(null);

        $this->expectException(Exception::class);
        $this->expectExceptionMessage('Accounts not found');

        $this->reversalService->reverseTransfer($userId, $data);
    }

    public function test_reverse_transfer_insufficient_balance()
    {
        $userId = 1;
        $transactionId = 100;
        $data = ['transaction_id' => $transactionId];

        $transaction = (object) ['id' => $transactionId, 'sender_id' => 1, 'receiver_id' => 2, 'amount' => 500];
        $senderAccount = (object) ['id' => 1, 'balance' => 1000];
        $recipientAccount = (object) ['id' => 2, 'balance' => 100];

        $this->transactionRepository->shouldReceive('findById')
            ->with($transactionId, $userId)
            ->andReturn($transaction);

        $this->accountRepository->shouldReceive('accountFindById')
            ->with(1)
            ->andReturn($senderAccount);

        $this->accountRepository->shouldReceive('accountFindById')
            ->with(2)
            ->andReturn($recipientAccount);

        $this->expectException(Exception::class);
        $this->expectExceptionMessage('Insufficient balance for reversal');

        $this->reversalService->reverseTransfer($userId, $data);
    }

    public function test_reverse_deposit_success()
    {
        $data = ['deposit_id' => 200, 'reason' => 'Duplicate deposit'];

        $deposit = (object) ['id' => 200, 'receiver_id' => 3, 'amount' => 300];
        $account = (object) ['id' => 3, 'balance' => 500];

        $this->transactionRepository->shouldReceive('findDepositById')
            ->with(200)
            ->andReturn($deposit);

        $this->accountRepository->shouldReceive('accountFindById')
            ->with(3)
            ->andReturn($account);

        $this->reversalRepository->shouldReceive('create')->once();
        $this->accountRepository->shouldReceive('updateBalance')->with(3, -300)->once();

        $this->reversalService->reverseDeposit($data);
        $this->assertTrue(true);
    }

    public function test_reverse_deposit_not_found()
    {
        $data = ['deposit_id' => 999];

        $this->transactionRepository->shouldReceive('findDepositById')
            ->with(999)
            ->andReturn(null);

        $this->expectException(Exception::class);
        $this->expectExceptionMessage('Invalid deposit or unauthorized reversal');

        $this->reversalService->reverseDeposit($data);
    }

    public function test_reverse_deposit_account_not_found()
    {
        $data = ['deposit_id' => 200];

        $deposit = (object) ['id' => 200, 'receiver_id' => 3, 'amount' => 300];

        $this->transactionRepository->shouldReceive('findDepositById')
            ->with(200)
            ->andReturn($deposit);

        $this->accountRepository->shouldReceive('accountFindById')
            ->with(3)
            ->andReturn(null);

        $this->expectException(Exception::class);
        $this->expectExceptionMessage('Account not found');

        $this->reversalService->reverseDeposit($data);
    }

    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }
}
