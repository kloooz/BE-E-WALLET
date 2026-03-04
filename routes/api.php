<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\WalletController;
use App\Http\Controllers\TransactionController;
use App\Http\Controllers\QrPaymentController;
use App\Http\Controllers\WebhookController;

// Public routes
Route::post('register', [AuthController::class, 'register']);
Route::post('login', [AuthController::class, 'login']);
Route::post('forgot-password', [AuthController::class, 'forgotPassword']);
Route::post('reset-password', [AuthController::class, 'resetPassword']);

// Webhook routes
Route::post('webhook/midtrans', [WebhookController::class, 'midtransHandler']);

// Protected routes
Route::middleware('auth:sanctum')->group(function () {
    Route::get('balance', [WalletController::class, 'balance']);
    Route::post('topup', [WalletController::class, 'topUp']);
    Route::post('transfer', [WalletController::class, 'transfer']);
    Route::get('transactions', [TransactionController::class, 'index']);
    
    // QR endpoints
    Route::get('dummy-qr', [QrPaymentController::class, 'generateDummyQr']);
    Route::post('scan-qr', [QrPaymentController::class, 'scanQrPayment']);
});
?>
