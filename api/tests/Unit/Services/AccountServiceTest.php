<?php

namespace Tests\Unit\Services;

use Tests\TestCase;
use App\Services\AccountService;
use App\Repositories\AccountRepository;
use Mockery;
use Exception;

class AccountServiceTest extends TestCase
{
    protected $accountService;
    protected $accountRepository;

    protected function setUp(): void
    {
        parent::setUp();
        $this->accountRepository = Mockery::mock(AccountRepository::class);
        $this->accountService = new AccountService($this->accountRepository);
    }

    public function test_get_all_accounts()
    {
        $userId = 1;
        $accounts = [['id' => 1, 'name' => 'Conta Corrente', 'balance' => 1000]];

        $this->accountRepository->shouldReceive('findAllByUserId')
            ->with($userId)
            ->andReturn($accounts);

        $result = $this->accountService->getAll($userId);
        $this->assertEquals($accounts, $result);
    }

    public function test_get_account_by_id()
    {
        $userId = 1;
        $accountId = 1;
        $account = ['id' => 1, 'name' => 'Conta Corrente', 'balance' => 1000];

        $this->accountRepository->shouldReceive('findById')
            ->with($accountId, $userId)
            ->andReturn($account);

        $result = $this->accountService->getById($userId, $accountId);
        $this->assertEquals($account, $result);
    }

    public function test_get_account_by_id_not_found()
    {
        $userId = 1;
        $accountId = 99;

        $this->accountRepository->shouldReceive('findById')
            ->with($accountId, $userId)
            ->andReturn(null);

        $this->expectException(Exception::class);
        $this->expectExceptionMessage('Account not found');

        $this->accountService->getById($userId, $accountId);
    }

    public function test_create_account()
    {
        $userId = 1;
        $data = ['name' => 'Conta Poupança'];
        $createdAccount = ['id' => 2, 'user_id' => $userId, 'name' => 'Conta Poupança', 'balance' => 0];

        $this->accountRepository->shouldReceive('create')
            ->with(['user_id' => $userId, 'name' => $data['name']])
            ->andReturn($createdAccount);

        $result = $this->accountService->create($userId, $data);
        $this->assertEquals($createdAccount, $result);
    }

    public function test_delete_account_with_zero_balance()
    {
        $userId = 1;
        $accountId = 1;
        $account = (object) ['id' => 1, 'user_id' => $userId, 'balance' => 0];

        $this->accountRepository->shouldReceive('findById')
            ->with($accountId, $userId)
            ->andReturn($account);

        $this->accountRepository->shouldReceive('delete')
            ->with($accountId, $userId)
            ->andReturn(true);

        $result = $this->accountService->delete($userId, $accountId);
        $this->assertTrue($result);
    }

    public function test_delete_account_with_non_zero_balance()
    {
        $userId = 1;
        $accountId = 1;
        $account = (object) ['id' => 1, 'user_id' => $userId, 'balance' => 500];

        $this->accountRepository->shouldReceive('findById')
            ->with($accountId, $userId)
            ->andReturn($account);

        $this->expectException(Exception::class);
        $this->expectExceptionMessage('Account must have zero balance to be deleted');

        $this->accountService->delete($userId, $accountId);
    }

    public function test_delete_account_not_found()
    {
        $userId = 1;
        $accountId = 99;

        $this->accountRepository->shouldReceive('findById')
            ->with($accountId, $userId)
            ->andReturn(null);

        $this->expectException(Exception::class);
        $this->expectExceptionMessage('Account not found');

        $this->accountService->delete($userId, $accountId);
    }

    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }
}
