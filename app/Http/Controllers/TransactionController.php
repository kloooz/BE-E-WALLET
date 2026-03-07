<?php

namespace App\Http\Controllers;

use App\Services\TransactionService;
use App\Traits\ApiResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
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
        $baseUrl   = config('services.midtrans.is_production')
            ? 'https://api.midtrans.com/v2'
            : 'https://api.sandbox.midtrans.com/v2';

        $response = Http::withBasicAuth($serverKey, '')->get("{$baseUrl}/{$orderId}/status");

        if ($response->successful()) {
            $data              = $response->json();
            $transactionStatus = $data['transaction_status'] ?? null;

            if ($transactionStatus === 'capture' || $transactionStatus === 'settlement') {
                $paymentType = $data['payment_type'] ?? null;
                if ($paymentType) {
                    $transaction->description = 'Top up balance via ' . ucwords(str_replace('_', ' ', $paymentType));
                }

                $this->walletService->completeTopUp($transaction);

                $user = $transaction->user;
                if ($user) {
                    try {
                        Mail::to($user->email)->send(new TopUpSuccessMail($user, $transaction));
                    } catch (\Exception $mailEx) {
                        Log::warning('Gagal mengirim email TopUpSuccess (verify): ' . $mailEx->getMessage());
                    }
                }
            } elseif ($transactionStatus === 'cancel' || $transactionStatus === 'deny' || $transactionStatus === 'expire') {
                $transaction->status = 'failed';
                $transaction->save();
            }
        }

        return $this->success('Transaction status verified', $transaction->fresh());
    }

    /**
     * Public payment finish callback — called by Midtrans redirect after e-wallet payment (DANA, GoPay, etc.).
     * Auto-verifies transaction status, updates balance, then redirects to frontend.
     * No auth required — this is called via browser redirect from Midtrans.
     */
    public function paymentFinish(Request $request)
    {
        $orderId     = $request->query('order_id');
        $frontendUrl = config('services.midtrans.frontend_url', 'http://localhost:5173');

        if (!$orderId) {
            return redirect($frontendUrl . '/payment/finish?status=error&message=order_id_missing');
        }

        $transaction = Transaction::where('reference_id', $orderId)->first();

        if (!$transaction) {
            return redirect($frontendUrl . '/payment/finish?order_id=' . $orderId . '&status=not_found');
        }

        // Already processed — just redirect
        if ($transaction->status === 'success') {
            return redirect($frontendUrl . '/payment/finish?order_id=' . $orderId . '&status=success');
        }

        // Call Midtrans API to check actual payment status
        $serverKey = config('services.midtrans.server_key');
        $baseUrl   = config('services.midtrans.is_production')
            ? 'https://api.midtrans.com/v2'
            : 'https://api.sandbox.midtrans.com/v2';

        try {
            $response          = Http::withBasicAuth($serverKey, '')->get("{$baseUrl}/{$orderId}/status");
            $data              = $response->json();
            $transactionStatus = $data['transaction_status'] ?? null;

            Log::info("paymentFinish: order={$orderId} status={$transactionStatus}");

            if ($transactionStatus === 'settlement' || $transactionStatus === 'capture') {
                $paymentType = $data['payment_type'] ?? null;
                if ($paymentType) {
                    $transaction->description = 'Top up via ' . ucwords(str_replace('_', ' ', $paymentType));
                }

                $this->walletService->completeTopUp($transaction);

                $user = $transaction->user;
                if ($user) {
                    try {
                        Mail::to($user->email)->send(new TopUpSuccessMail($user, $transaction));
                    } catch (\Exception $mailEx) {
                        Log::warning('Gagal kirim email TopUpSuccess (finish): ' . $mailEx->getMessage());
                    }
                }

                return redirect($frontendUrl . '/payment/finish?order_id=' . $orderId . '&status=success');
            }

            if ($transactionStatus === 'pending') {
                return redirect($frontendUrl . '/payment/finish?order_id=' . $orderId . '&status=pending');
            }

            // cancel / deny / expire
            $transaction->status = 'failed';
            $transaction->save();
        } catch (\Exception $e) {
            Log::error('paymentFinish error: ' . $e->getMessage());
        }

        return redirect($frontendUrl . '/payment/finish?order_id=' . $orderId . '&status=failed');
    }
}
?>
