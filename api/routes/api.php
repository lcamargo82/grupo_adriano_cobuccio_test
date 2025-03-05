<?php

use App\Http\Controllers\AccountController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\ReversalController;
use App\Http\Controllers\TransactionController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::post('register', [AuthController::class, 'register']);
Route::post('login', [AuthController::class, 'login']);
Route::post('transactions/deposit', [TransactionController::class, 'deposit']);

Route::middleware('auth:api')->group(function () {
    Route::get('profile', [UserController::class, 'profile']);
    Route::put('profile', [UserController::class, 'updateProfile']);
    Route::delete('profile', [UserController::class, 'deleteProfile']);

    Route::get('accounts', [AccountController::class, 'index']);
    Route::get('accounts/{id}', [AccountController::class, 'show']);
    Route::post('accounts', [AccountController::class, 'store']);
    Route::delete('accounts/{id}', [AccountController::class, 'destroy']);

    Route::get('/transactions/{accountId}', [TransactionController::class, 'index']);
    Route::post('transactions/transfer', [TransactionController::class, 'transfer']);

    Route::post('reversals/transfer/{accountId}', [ReversalController::class, 'reverseTransaction']);
    Route::post('reversals/deposit/{accountId}', [ReversalController::class, 'reverseDeposit']);
});
