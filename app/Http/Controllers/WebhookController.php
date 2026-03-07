<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Transaction;
use App\Services\WalletService;
use App\Mail\TopUpSuccessMail;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;

class WebhookController extends Controller
{
    protected $walletService;

    public function __construct(WalletService $walletService)
    {
        $this->walletService = $walletService;
    }

    /**
     * Handle midtrans webhook notification.
     */
    public function midtransHandler(Request $request)
    {
        // For production, you should verify the signature key here:
        // $serverKey = config('services.midtrans.server_key');
        // $signatureKey = hash("sha512", $request->order_id . $request->status_code . $request->gross_amount . $serverKey);
        // if ($signatureKey !== $request->signature_key) {
        //     return response()->json(['message' => 'Invalid signature'], 403);
        // }

        $payload = $request->all();
        Log::info('Midtrans Webhook Payload: ', $payload);

        $orderId = $payload['order_id'] ?? null;
        $transactionStatus = $payload['transaction_status'] ?? null;

        if (!$orderId || !$transactionStatus) {
            return response()->json(['message' => 'Invalid payload'], 400);
        }

        $transaction = Transaction::where('reference_id', $orderId)->first();

        if (!$transaction) {
            return response()->json(['message' => 'Transaction not found'], 404);
        }

        // Only process if status is pending
        if ($transaction->status !== 'pending') {
            return response()->json(['message' => 'Transaction already processed'], 200);
        }

        if ($transactionStatus == 'capture' || $transactionStatus == 'settlement') {
            // Update description with payment method if available
            $paymentType = $payload['payment_type'] ?? null;
            if ($paymentType) {
                $transaction->description = 'Top up balance via ' . ucwords(str_replace('_', ' ', $paymentType));
                // Do not call save() here, it causes race conditions with completeTopUp
            }

            // Provide Topup (this function will lock and save the transaction status internally)
            $this->walletService->completeTopUp($transaction);

            // Send email
            $user = $transaction->user;
            if ($user) {
                try {
                    Mail::to($user->email)->send(new TopUpSuccessMail($user, $transaction));
                } catch (\Exception $mailEx) {
                    Log::warning('Gagal mengirim email TopUpSuccess (webhook): ' . $mailEx->getMessage());
                }
            }

        } else if ($transactionStatus == 'cancel' || $transactionStatus == 'deny' || $transactionStatus == 'expire') {
            // Fail Topup
            $transaction->status = 'failed';
            $transaction->save();
        } else if ($transactionStatus == 'pending') {
            // Still pending
        }

        return response()->json(['message' => 'Webhook processed successfully'], 200);
    }
}
