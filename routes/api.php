<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\WalletController;
use App\Http\Controllers\TransactionController;

// Public routes
Route::post('register', [AuthController::class, 'register']);
Route::post('login', [AuthController::class, 'login']);

// Protected routes
Route::middleware('auth:sanctum')->group(function () {
    Route::get('balance', [WalletController::class, 'balance']);
    Route::post('topup', [WalletController::class, 'topUp']);
    Route::post('transfer', [WalletController::class, 'transfer']);
    Route::get('transactions', [TransactionController::class, 'index']);
});
?>
