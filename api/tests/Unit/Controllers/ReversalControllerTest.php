<?php

namespace Tests\Unit\Controllers;

use Tests\TestCase;
use App\Services\ReversalService;
use App\Http\Controllers\ReversalController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Mockery;

class ReversalControllerTest extends TestCase
{
    protected $reversalService;
    protected $reversalController;

    protected function setUp(): void
    {
        parent::setUp();

        $this->reversalService = Mockery::mock(ReversalService::class);
        $this->reversalController = new ReversalController($this->reversalService);
    }

    public function test_reverse_transaction_success()
    {
        Auth::shouldReceive('id')->andReturn(1);
        $request = Request::create('/api/reversals/transaction', 'POST', [
            'transaction_id' => 10,
            'reason' => 'Sent to the wrong account'
        ]);

        $this->reversalService->shouldReceive('reverseTransfer')->once()->with(1, $request->all());

        $response = $this->reversalController->reverseTransaction($request);

        $this->assertEquals(200, $response->getStatusCode());
        $this->assertEquals(['message' => 'Transaction reversed successfully'], $response->getData(true));
    }

    public function test_reverse_deposit_success()
    {
        $request = Request::create('/api/reversals/deposit', 'POST', [
            'deposit_id' => 5,
            'reason' => 'Deposit sent to the wrong account'
        ]);

        $this->reversalService->shouldReceive('reverseDeposit')->once()->with($request->all());

        $response = $this->reversalController->reverseDeposit($request);

        $this->assertEquals(200, $response->getStatusCode());
        $this->assertEquals(['message' => 'Deposit reversed successfully'], $response->getData(true));
    }
}
