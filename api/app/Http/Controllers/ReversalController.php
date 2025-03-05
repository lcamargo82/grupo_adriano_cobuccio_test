<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\ReversalService;
use Illuminate\Support\Facades\Auth;
use Exception;

class ReversalController extends Controller
{
    protected $reversalService;

    public function __construct(ReversalService $reversalService)
    {
        $this->reversalService = $reversalService;
    }

    /**
     * @OA\Post(
     *     path="/api/reversals/transaction",
     *     summary="Reverse a transaction",
     *     tags={"Reversal"},
     *     security={{"bearerAuth":{}}},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"transaction_id", "reason"},
     *             @OA\Property(property="account_id", type="integer", example=72, description="ID of the account"),
     *             @OA\Property(property="transaction_id", type="integer", example=10, description="ID of the transaction to be reversed"),
     *             @OA\Property(property="reason", type="string", example="Sent to the wrong account", description="Reason for the reversal")
     *         )
     *     ),
     *     @OA\Response(response=200, description="Transaction reversed successfully"),
     *     @OA\Response(response=400, description="Invalid transaction or insufficient balance")
     * )
     */
    public function reverseTransaction(Request $request)
    {
        try {
            $this->reversalService->reverseTransfer(Auth::id(), $request->all());
            return response()->json(['message' => 'Transaction reversed successfully']);
        } catch (Exception $e) {
            return response()->json(['error' => $e->getMessage()], $e->getCode() ?: 500);
        }
    }

    /**
     * @OA\Post(
     *     path="/api/reversals/deposit",
     *     summary="Reverse a deposit",
     *     tags={"Reversal"},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"deposit_id", "reason"},
     *             @OA\Property(property="account_id", type="integer", example=72, description="ID of the account"),
     *             @OA\Property(property="transaction_id", type="integer", example=5, description="ID of the deposit to be reversed"),
     *             @OA\Property(property="reason", type="string", example="Deposit sent to the wrong account", description="Reason for the reversal")
     *         )
     *     ),
     *     @OA\Response(response=200, description="Deposit reversed successfully"),
     *     @OA\Response(response=400, description="Invalid deposit or insufficient balance")
     * )
     */
    public function reverseDeposit(Request $request)
    {
        try {
            $this->reversalService->reverseDeposit(Auth::id(), $request->all());
            return response()->json(['message' => 'Deposit reversed successfully']);
        } catch (Exception $e) {
            return response()->json(['error' => $e->getMessage()], $e->getCode() ?: 500);
        }
    }
}
