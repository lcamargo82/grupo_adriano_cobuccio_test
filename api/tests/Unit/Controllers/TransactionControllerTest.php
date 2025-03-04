<?php

namespace Tests\Unit\Controllers;

use App\Http\Controllers\TransactionController;
use App\Services\TransactionService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Mockery;
use Tests\TestCase;

class TransactionControllerTest extends TestCase
{
    protected $transactionServiceMock;
    protected $transactionController;

    protected function setUp(): void
    {
        parent::setUp();

        $this->transactionServiceMock = Mockery::mock(TransactionService::class);
        $this->transactionController = new TransactionController($this->transactionServiceMock);
    }

    public function test_index_success()
    {
        $transactions = [['id' => 1, 'amount' => 100.00, 'type' => 'deposit']];

        Auth::shouldReceive('id')->once()->andReturn(1);
        $this->transactionServiceMock->shouldReceive('getAll')->once()->with(1)->andReturn($transactions);

        $response = $this->transactionController->index();

        $this->assertInstanceOf(JsonResponse::class, $response);
        $this->assertEquals(200, $response->getStatusCode());
        $this->assertEquals($transactions, $response->getData(true));
    }

    public function test_show_success()
    {
        $transaction = ['id' => 1, 'amount' => 100.00, 'type' => 'deposit'];

        Auth::shouldReceive('id')->once()->andReturn(1);
        $this->transactionServiceMock->shouldReceive('getById')->once()->with(1, 1)->andReturn($transaction);

        $response = $this->transactionController->show(1);

        $this->assertInstanceOf(JsonResponse::class, $response);
        $this->assertEquals(200, $response->getStatusCode());
        $this->assertEquals($transaction, $response->getData(true));
    }

    public function test_store_success()
    {
        $requestData = ['amount' => 50.00, 'type' => 'withdrawal'];
        $createdTransaction = ['id' => 2, 'amount' => 50.00, 'type' => 'withdrawal'];

        Auth::shouldReceive('id')->once()->andReturn(1);
        $this->transactionServiceMock->shouldReceive('create')->once()->with(1, $requestData)->andReturn($createdTransaction);

        $request = Request::create('/api/transactions', 'POST', $requestData);

        $response = $this->transactionController->store($request);

        $this->assertInstanceOf(JsonResponse::class, $response);
        $this->assertEquals(201, $response->getStatusCode());
        $this->assertEquals($createdTransaction, $response->getData(true));
    }

    public function test_destroy_success()
    {
        Auth::shouldReceive('id')->once()->andReturn(1);
        $this->transactionServiceMock->shouldReceive('delete')->once()->with(1, 1)->andReturn(true);

        $response = $this->transactionController->destroy(1);

        $this->assertInstanceOf(JsonResponse::class, $response);
        $this->assertEquals(200, $response->getStatusCode());
        $this->assertEquals(['message' => 'Transaction deleted successfully'], $response->getData(true));
    }

    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }
}
