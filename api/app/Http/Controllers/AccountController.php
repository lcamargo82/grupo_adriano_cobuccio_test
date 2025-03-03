<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\AccountService;
use Illuminate\Support\Facades\Auth;
use Exception;

class AccountController extends Controller
{
    protected $accountService;

    public function __construct(AccountService $accountService)
    {
        $this->accountService = $accountService;
    }

    /**
     * @OA\Get(
     *     path="/api/accounts",
     *     summary="Get all accounts of the authenticated user",
     *     tags={"Account"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Response(response=200, description="List of user accounts")
     * )
     */
    public function index()
    {
        try {
            return response()->json($this->accountService->getAll(Auth::id()));
        } catch (Exception $e) {
            return response()->json(['error' => $e->getMessage()], 400);
        }
    }

    /**
     * @OA\Post(
     *     path="/api/accounts",
     *     summary="Create a new account",
     *     tags={"Account"},
     *     security={{"bearerAuth":{}}},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"name"},
     *             @OA\Property(property="name", type="string", example="Savings Account")
     *         )
     *     ),
     *     @OA\Response(response=201, description="Account created successfully")
     * )
     */
    public function store(Request $request)
    {
        try {
            return response()->json($this->accountService->create(Auth::id(), $request->all()), 201);
        } catch (Exception $e) {
            return response()->json(['error' => $e->getMessage()], 400);
        }
    }

    /**
     * @OA\Delete(
     *     path="/api/accounts/{id}",
     *     summary="Delete an account if balance is zero",
     *     tags={"Account"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(response=200, description="Account deleted successfully"),
     *     @OA\Response(response=400, description="Account must have zero balance to be deleted")
     * )
     */
    public function destroy($id)
    {
        try {
            $this->accountService->delete(Auth::id(), $id);
            return response()->json(['message' => 'Account deleted successfully']);
        } catch (Exception $e) {
            return response()->json(['error' => $e->getMessage()], 400);
        }
    }
}
