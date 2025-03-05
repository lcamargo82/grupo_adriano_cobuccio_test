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
     * @OA\Get(
     *     path="/api/transactions",
     *     summary="Get all transactions of the logged-in user for a specific account",
     *     tags={"Transaction"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="account_id",
     *         in="query",
     *         required=true,
     *         description="The ID of the account to fetch transactions for",
     *         @OA\Schema(type="integer", example=1)
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="List of transactions",
     *         @OA\JsonContent(
     *             type="array",
     *             @OA\Items(
     *                 type="object",
     *                 @OA\Property(property="type", type="string", example="receiver", description="Type of the transaction (receiver = received, sender = sent)"),
     *                 @OA\Property(property="createdAt", type="string", format="date-time", example="05/03/2025 14:30:00", description="Creation date in Brazilian format (dd/mm/yyyy hh:mm:ss)"),
     *                 @OA\Property(property="amount", type="number", format="float", example=200.50, description="Transaction amount (negative for sent transactions)"),
     *                 @OA\Property(property="receiver_id", type="integer", example=2, description="ID of the receiver account, null for sent transactions")
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Unauthorized",
     *         @OA\JsonContent(
     *             @OA\Property(property="error", type="string", example="Unauthorized"),
     *             @OA\Property(property="message", type="string", example="Token not provided or invalid.")
     *         )
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Internal Server Error",
     *         @OA\JsonContent(
     *             @OA\Property(property="error", type="string", example="Internal Server Error"),
     *             @OA\Property(property="message", type="string", example="An unexpected error occurred while fetching the transactions.")
     *         )
     *     )
     * )
     */
    public function index(Request $request, $accountId)
    {
        $user = auth()->user();
        if (!$user) {
            return response()->json(['error' => 'User not authenticated'], 401);
        }

        $transactions = $this->transactionService->getAccountTransactions($user, $accountId);

        return response()->json($transactions);
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
