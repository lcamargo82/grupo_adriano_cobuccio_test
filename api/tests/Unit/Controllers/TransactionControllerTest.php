<?php

namespace Tests\Unit\Controllers;

use Illuminate\Testing\TestResponse;
use Tests\TestCase;
use Mockery;
use Illuminate\Http\Request;
use App\Http\Controllers\TransactionController;
use App\Services\TransactionService;
use Illuminate\Support\Facades\Auth;

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

    public function test_transfer_success()
    {
        Auth::shouldReceive('id')->andReturn(1);

        $request = Request::create('/api/transactions/transfer', 'POST', [
            'sender_id' => 1,
            'recipient_id' => 2,
            'amount' => 100,
            'type' => 'transfer',
        ]);

        $this->transactionServiceMock
            ->shouldReceive('transfer')
            ->once()
            ->with(1, $request->all())
            ->andReturn(true);

        $response = $this->transactionController->transfer($request);

        $response = new TestResponse($response);

        $response->assertJson(['message' => 'Transfer successful']);
    }

    public function test_deposit_success()
    {
        $request = Request::create('/api/transactions/deposit', 'POST', [
            'recipient_id' => 2,
            'amount' => 200,
            'type' => 'deposit',
        ]);

        $this->transactionServiceMock
            ->shouldReceive('deposit')
            ->once()
            ->with($request->all())
            ->andReturn(true);

        $response = $this->transactionController->deposit($request);

        $response = new TestResponse($response);

        $response->assertJson(['message' => 'Deposit successful']);
    }
}
