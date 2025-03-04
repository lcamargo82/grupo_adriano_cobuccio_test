<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\TransactionService;
use Illuminate\Support\Facades\Auth;
use Exception;

class TransactionController extends Controller
{
    protected $transactionService;

    public function __construct(TransactionService $transactionService)
    {
        $this->transactionService = $transactionService;
    }

    /**
     * @OA\Post(
     *     path="/api/transactions/transfer",
     *     summary="Transfer money between accounts",
     *     tags={"Transaction"},
     *     security={{"bearerAuth":{}}},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"sender_id", "recipient_id", "amount", "type"},
     *             @OA\Property(property="sender_id", type="integer", example=72, description="ID of the sender's account"),
     *             @OA\Property(property="recipient_id", type="integer", example=2, description="ID of the recipient's account"),
     *             @OA\Property(property="amount", type="number", format="float", example=100),
     *             @OA\Property(property="type", type="string", example="transfer")
     *         )
     *     ),
     *     @OA\Response(response=200, description="Transfer successful"),
     *     @OA\Response(response=400, description="Insufficient balance or invalid accounts")
     * )
     */
    public function transfer(Request $request)
    {
        try {
            $this->transactionService->transfer(Auth::id(), $request->all());
            return response()->json(['message' => 'Transfer successful']);
        } catch (Exception $e) {
            return response()->json(['error' => $e->getMessage()], $e->getCode() ?: 500);
        }
    }

    /**
     * @OA\Post(
     *     path="/api/transactions/deposit",
     *     summary="Deposit money into an account",
     *     tags={"Transaction"},
     *     security={{"bearerAuth":{}}},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"recipient_id", "amount", "type"},
     *             @OA\Property(property="recipient_id", type="integer", example=72, description="ID of the account receiving the deposit"),
     *             @OA\Property(property="amount", type="number", format="float", example=200),
     *             @OA\Property(property="type", type="string", example="deposit")
     *         )
     *     ),
     *     @OA\Response(response=200, description="Deposit successful"),
     *     @OA\Response(response=400, description="Invalid account or unauthorized deposit")
     * )
     */
    public function deposit(Request $request)
    {
        try {
            $this->transactionService->deposit($request->all());
            return response()->json(['message' => 'Deposit successful']);
        } catch (Exception $e) {
            return response()->json(['error' => $e->getMessage()], $e->getCode() ?: 500);
        }
    }
}
