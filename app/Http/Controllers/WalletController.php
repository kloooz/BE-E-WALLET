<?php

namespace App\Http\Controllers;

use App\Http\Requests\TopUpRequest;
use App\Http\Requests\TransferRequest;
use App\Services\WalletService;
use App\Services\TransferService;
use App\Traits\ApiResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use App\Mail\TopUpSuccessMail;

class WalletController
{
    use ApiResponse;

    protected $walletService;
    protected $transferService;
    protected $midtransService;

    public function __construct(WalletService $walletService, TransferService $transferService, \App\Services\MidtransService $midtransService)
    {
        $this->walletService = $walletService;
        $this->transferService = $transferService;
        $this->midtransService = $midtransService;
    }

    /**
     * Get current wallet balance.
     */
    public function balance(): JsonResponse
    {
        $user = Auth::user();
        $balance = $this->walletService->getBalance($user);
        return $this->success('Balance retrieved', ['balance' => $balance]);
    }

    /**
     * Top up wallet balance (initiates Midtrans payment).
     */
    public function topUp(TopUpRequest $request): JsonResponse
    {
        $user = Auth::user();
        $amount = (int) $request->validated()['amount'];
        $transaction = $this->walletService->topUp($user, $amount);
        
        try {
            $snapToken = $this->midtransService->generateSnapToken($transaction, $user);
            
            // Save snap token to transaction
            $transaction->snap_token = $snapToken;
            $transaction->save();
            
            // Send pending email with Snap Link
            Mail::to($user->email)->send(new \App\Mail\TopUpPendingMail($user, $transaction, $snapToken));

            return $this->success('Top up initiated', [
                'transaction' => $transaction,
                'snap_token' => $snapToken
            ], 201);
        } catch (\Exception $e) {
            // If failed to generate token, we might want to fail the transaction
            $transaction->status = 'failed';
            $transaction->save();
            return $this->error('Failed to generate Midtrans snap token: ' . $e->getMessage(), null, 400);
        }
    }

    /**
     * Transfer balance to another user.
     */
    public function transfer(TransferRequest $request): JsonResponse
    {
        $user = Auth::user();
        $data = $request->validated();
        $identifier = $data['identifier'];
        $amount = (int) $data['amount'];
        $pin = $data['pin'];

        if (!\Illuminate\Support\Facades\Hash::check($pin, $user->pin)) {
            return $this->error('PIN yang Anda masukkan salah', null, 400);
        }

        try {
            $result = $this->transferService->transfer($user, $identifier, $amount);
            return $this->success('Transfer successful', $result, 201);
        } catch (\Exception $e) {
            return $this->error($e->getMessage(), null, 400);
        }
    }
    public function purchase(Request $request): JsonResponse
    {
        $user = Auth::user();
        $validated = $request->validate([
            'promoId' => 'required|string',
            'promoTitle' => 'required|string',
            'amount' => 'required|numeric|min:1',
            'pin' => 'required|string'
        ]);

        if (!\Illuminate\Support\Facades\Hash::check($validated['pin'], $user->pin)) {
            return $this->error('PIN yang Anda masukkan salah', null, 400);
        }

        try {
            $transaction = $this->walletService->purchasePromo($user, $validated['promoId'], $validated['promoTitle'], (int) $validated['amount']);
            
            // Generate a random voucher code
            $voucherCode = strtoupper(\Illuminate\Support\Str::random(10));
            
            // Send email
            Mail::to($user->email)->send(new \App\Mail\PromoVoucherMail($user, $transaction, $voucherCode));

            return $this->success('Promo purchase successful', [
                'transaction' => $transaction,
                'voucher_code' => $voucherCode
            ], 200);
        } catch (\Exception $e) {
            return $this->error($e->getMessage(), null, 400);
        }
    }
}
?>
