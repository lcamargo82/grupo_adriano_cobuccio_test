<?php

namespace Tests\Unit\Controllers;

use App\Http\Controllers\AccountController;
use App\Services\AccountService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Mockery;
use Tests\TestCase;

class AccountControllerTest extends TestCase
{
    protected $accountServiceMock;
    protected $accountController;

    protected function setUp(): void
    {
        parent::setUp();

        $this->accountServiceMock = Mockery::mock(AccountService::class);
        $this->accountController = new AccountController($this->accountServiceMock);
    }

    public function test_index_success()
    {
        $accounts = [['id' => 1, 'name' => 'Savings Account']];

        Auth::shouldReceive('id')->once()->andReturn(1);
        $this->accountServiceMock->shouldReceive('getAll')->once()->with(1)->andReturn($accounts);

        $response = $this->accountController->index();

        $this->assertInstanceOf(JsonResponse::class, $response);
        $this->assertEquals(200, $response->getStatusCode());
        $this->assertEquals($accounts, $response->getData(true));
    }

    public function test_show_success()
    {
        $account = ['id' => 1, 'name' => 'Savings Account'];

        Auth::shouldReceive('id')->once()->andReturn(1);
        $this->accountServiceMock->shouldReceive('getById')->once()->with(1, 1)->andReturn($account);

        $response = $this->accountController->show(1);

        $this->assertInstanceOf(JsonResponse::class, $response);
        $this->assertEquals(200, $response->getStatusCode());
        $this->assertEquals($account, $response->getData(true));
    }

    public function test_store_success()
    {
        $requestData = ['name' => 'Checking Account'];
        $createdAccount = ['id' => 2, 'name' => 'Checking Account'];

        Auth::shouldReceive('id')->once()->andReturn(1);
        $this->accountServiceMock->shouldReceive('create')->once()->with(1, $requestData)->andReturn($createdAccount);

        $request = Request::create('/api/accounts', 'POST', $requestData);

        $response = $this->accountController->store($request);

        $this->assertInstanceOf(JsonResponse::class, $response);
        $this->assertEquals(201, $response->getStatusCode());
        $this->assertEquals($createdAccount, $response->getData(true));
    }

    public function test_destroy_success()
    {
        Auth::shouldReceive('id')->once()->andReturn(1);
        $this->accountServiceMock->shouldReceive('delete')->once()->with(1, 1)->andReturn(true);

        $response = $this->accountController->destroy(1);

        $this->assertInstanceOf(JsonResponse::class, $response);
        $this->assertEquals(200, $response->getStatusCode());
        $this->assertEquals(['message' => 'Account deleted successfully'], $response->getData(true));
    }

    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }
}
