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

// Payment finish callback — public (called by Midtrans redirect after e-wallet payment)
// Auto-verifies transaction status then redirects to frontend
Route::get('payment/finish', [TransactionController::class, 'paymentFinish']);

// Protected routes
Route::middleware('auth:sanctum')->group(function () {
    Route::get('profile', [AuthController::class, 'getProfile']);
    Route::get('balance', [WalletController::class, 'balance']);
    Route::post('topup', [WalletController::class, 'topUp']);
    Route::post('transfer', [WalletController::class, 'transfer']);
    Route::post('purchase', [WalletController::class, 'purchase']);
    Route::get('transactions', [TransactionController::class, 'index']);
    Route::post('transactions/{id}/verify', [TransactionController::class, 'verify']);
    Route::get('transactions/{id}/verify', [TransactionController::class, 'verify']);
    Route::post('pin', [AuthController::class, 'updatePin']);
    Route::put('profile', [AuthController::class, 'updateProfile']);
    
    // QR endpoints
    Route::get('dummy-qr', [QrPaymentController::class, 'generateDummyQr']);
    Route::post('scan-qr', [QrPaymentController::class, 'scanQrPayment']);
});
?>
