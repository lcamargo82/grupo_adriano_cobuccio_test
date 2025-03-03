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
     *     summary="Transfer money between users",
     *     tags={"Transaction"},
     *     security={{"bearerAuth":{}}},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"recipient_id","amount"},
     *             @OA\Property(property="recipient_id", type="integer", example=2),
     *             @OA\Property(property="amount", type="number", format="float", example=100.50)
     *         )
     *     ),
     *     @OA\Response(response=200, description="Transfer successful"),
     *     @OA\Response(response=400, description="Insufficient balance or unauthorized user")
     * )
     */
    public function transfer(Request $request)
    {
        try {
            $this->transactionService->transfer(Auth::id(), $request->all());
            return response()->json(['message' => 'Transfer successful']);
        } catch (Exception $e) {
            return response()->json(['error' => $e->getMessage()], 400);
        }
    }

    /**
     * @OA\Post(
     *     path="/api/transactions/deposit",
     *     summary="Deposit money into account",
     *     tags={"Transaction"},
     *     security={{"bearerAuth":{}}},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"amount"},
     *             @OA\Property(property="amount", type="number", format="float", example=200.00)
     *         )
     *     ),
     *     @OA\Response(response=200, description="Deposit successful")
     * )
     */
    public function deposit(Request $request)
    {
        try {
            $this->transactionService->deposit(Auth::id(), $request->all());
            return response()->json(['message' => 'Deposit successful']);
        } catch (Exception $e) {
            return response()->json(['error' => $e->getMessage()], 400);
        }
    }
}
