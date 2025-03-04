<?php

namespace Tests\Unit\Services;

use Tests\TestCase;
use App\Services\TransactionService;
use App\Repositories\TransactionRepository;
use App\Repositories\AccountRepository;
use Mockery;
use Exception;

class TransactionServiceTest extends TestCase
{
    protected $transactionService;
    protected $transactionRepository;
    protected $accountRepository;

    protected function setUp(): void
    {
        parent::setUp();

        $this->transactionRepository = Mockery::mock(TransactionRepository::class);
        $this->accountRepository = Mockery::mock(AccountRepository::class);

        $this->transactionService = new TransactionService(
            $this->transactionRepository,
            $this->accountRepository
        );
    }

    public function test_transfer_success()
    {
        $userId = 1;
        $data = ['sender_id' => 1, 'recipient_id' => 2, 'amount' => 200];

        $senderAccount = (object) ['id' => 1, 'balance' => 500];
        $recipientAccount = (object) ['id' => 2, 'balance' => 300];

        $this->accountRepository->shouldReceive('findById')
            ->with(1, $userId)
            ->andReturn($senderAccount);

        $this->accountRepository->shouldReceive('accountFindById')
            ->with(2)
            ->andReturn($recipientAccount);

        $this->transactionRepository->shouldReceive('createTransfer')
            ->once()
            ->with(1, 2, 200);

        $this->accountRepository->shouldReceive('updateBalance')->with(1, -200)->once();
        $this->accountRepository->shouldReceive('updateBalance')->with(2, 200)->once();

        $this->transactionService->transfer($userId, $data);
        $this->assertTrue(true);
    }

    public function test_transfer_invalid_accounts()
    {
        $userId = 1;
        $data = ['sender_id' => 1, 'recipient_id' => 2, 'amount' => 200];

        $this->accountRepository->shouldReceive('findById')
            ->with(1, $userId)
            ->andReturn(null);

        $this->expectException(Exception::class);

        $this->transactionService->transfer($userId, $data);
    }

    public function test_transfer_insufficient_balance()
    {
        $userId = 1;
        $data = ['sender_id' => 1, 'recipient_id' => 2, 'amount' => 500];

        $senderAccount = (object) ['id' => 1, 'balance' => 300];
        $recipientAccount = (object) ['id' => 2, 'balance' => 100];

        $this->accountRepository->shouldReceive('findById')
            ->with(1, $userId)
            ->andReturn($senderAccount);

        $this->accountRepository->shouldReceive('accountFindById')
            ->with(2)
            ->andReturn($recipientAccount);

        $this->expectException(Exception::class);
        $this->expectExceptionMessage('Insufficient balance');

        $this->transactionService->transfer($userId, $data);
    }

    public function test_deposit_success()
    {
        $data = ['receiver_id' => 3, 'amount' => 400];
        $account = (object) ['id' => 3, 'balance' => 600];

        $this->accountRepository->shouldReceive('accountFindById')
            ->with(3)
            ->andReturn($account);

        $this->transactionRepository->shouldReceive('createDeposit')
            ->once()
            ->with(3, 400);

        $this->accountRepository->shouldReceive('updateBalance')->with(3, 400)->once();

        $this->transactionService->deposit($data);
        $this->assertTrue(true);
    }

    public function test_deposit_account_not_found()
    {
        $data = ['receiver_id' => 3, 'amount' => 400];

        $this->accountRepository->shouldReceive('accountFindById')
            ->with(3)
            ->andReturn(null);

        $this->expectException(Exception::class);
        $this->expectExceptionMessage('Account not found');

        $this->transactionService->deposit($data);
    }

    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }
}
