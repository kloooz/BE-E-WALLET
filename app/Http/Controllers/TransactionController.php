<?php

namespace App\Http\Controllers;

use App\Services\TransactionService;
use App\Traits\ApiResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use App\Models\Transaction;
use App\Services\WalletService;
use App\Mail\TopUpSuccessMail;
use Illuminate\Support\Facades\Mail;

class TransactionController
{
    use ApiResponse;

    protected $transactionService;
    protected $walletService;

    public function __construct(TransactionService $transactionService, WalletService $walletService)
    {
        $this->transactionService = $transactionService;
        $this->walletService = $walletService;
    }

    /**
     * Get paginated transaction history for the authenticated user.
     */
    public function index(): JsonResponse
    {
        $user = Auth::user();
        $transactions = $this->transactionService->getUserTransactions($user);
        return $this->success('Transaction history retrieved', $transactions);
    }

    /**
     * Manually verify and sync transaction status from Midtrans.
     */
    public function verify(Request $request, $orderId): JsonResponse
    {
        $transaction = Transaction::where('reference_id', $orderId)->first();
        if (!$transaction) {
            return $this->error('Transaction not found', null, 404);
        }

        if ($transaction->status !== 'pending') {
            return $this->success('Transaction already processed', $transaction);
        }

        $serverKey = config('services.midtrans.server_key');
        $baseUrl = config('services.midtrans.is_production') 
            ? 'https://api.midtrans.com/v2' 
            : 'https://api.sandbox.midtrans.com/v2';

        $response = Http::withBasicAuth($serverKey, '')->get("{$baseUrl}/{$orderId}/status");

        if ($response->successful()) {
            $data = $response->json();
            $transactionStatus = $data['transaction_status'] ?? null;

            if ($transactionStatus == 'capture' || $transactionStatus == 'settlement') {
                $paymentType = $data['payment_type'] ?? null;
                if ($paymentType) {
                    $transaction->description = 'Top up balance via ' . ucwords(str_replace('_', ' ', $paymentType));
                    // do not save here, wait for completeTopUp lock
                }

                $this->walletService->completeTopUp($transaction);
                $user = $transaction->user;
                if ($user) {
                    Mail::to($user->email)->send(new TopUpSuccessMail($user, $transaction));
                }
            } else if ($transactionStatus == 'cancel' || $transactionStatus == 'deny' || $transactionStatus == 'expire') {
                $transaction->status = 'failed';
                $transaction->save();
            }
        }

        return $this->success('Transaction status verified', $transaction->fresh());
    }
}
?>
