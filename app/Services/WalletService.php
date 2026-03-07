<?php

namespace App\Services;

use App\Models\User;
use App\Models\Wallet;
use App\Models\Transaction;
use Illuminate\Support\Facades\DB;

class WalletService
{
    /**
     * Get the current balance of the given user's wallet.
     *
     * @param User $user
     * @return int
     */
    public function getBalance(User $user): int
    {
        // Ensure wallet relationship is loaded
        $wallet = $user->wallet;
        return $wallet ? (int) $wallet->balance : 0;
    }

    /**
     * Top up the user's wallet by a given amount (pending via Midtrans).
     *
     * @param User $user
     * @param int $amount
     * @return Transaction
     */
    public function topUp(User $user, int $amount): Transaction
    {
        return DB::transaction(function () use ($user, $amount) {
            // Create a pending top‑up transaction record
            return Transaction::create([
                'user_id'      => $user->id,
                'type'         => 'topup',
                'amount'       => $amount,
                'reference_id' => 'TOP-' . (string) \Illuminate\Support\Str::uuid(),
                'description'  => 'Top up balance via Midtrans',
                'status'       => 'pending',
            ]);
        });
    }

    /**
     * Complete a pending top-up transaction.
     *
     * @param Transaction $transaction
     * @return Transaction
     */
    public function completeTopUp(Transaction $transaction): Transaction
    {
        return DB::transaction(function () use ($transaction) {
            // Lock the wallet row for update
            $wallet = Wallet::where('user_id', $transaction->user_id)->lockForUpdate()->first();
            
            if (!$wallet) {
                // If wallet doesn't exist for some reason, create it
                $wallet = Wallet::create(['user_id' => $transaction->user_id, 'balance' => 0]);
            }
            
            $wallet->balance += $transaction->amount;
            $wallet->save();

            $transaction->status = 'success';
            $transaction->save();
            
            return $transaction;
        });
    }

    /**
     * Process a QR payment.
     *
     * @param User $user
     * @param array $qrData
     * @return Transaction
     * @throws \Exception
     */
    public function qrPayment(User $user, array $qrData): Transaction
    {
        return DB::transaction(function () use ($user, $qrData) {
            $wallet = Wallet::where('user_id', $user->id)->lockForUpdate()->first();
            $amount = $qrData['amount'];
            
            if (!$wallet || $wallet->balance < $amount) {
                throw new \Exception('Insufficient balance for this payment.');
            }

            $wallet->balance -= $amount;
            $wallet->save();

            // Create a payment transaction record
            return Transaction::create([
                'user_id' => $user->id,
                'type'    => 'payment',
                'amount'  => $amount,
                'reference_id' => $qrData['transaction_id'] ?? uniqid('QR_'),
                'description' => 'Payment to ' . $qrData['merchant_name'],
            ]);
        });
    }
    public function purchasePromo(User $user, string $promoId, string $promoTitle, int $amount): Transaction
    {
        return DB::transaction(function () use ($user, $promoId, $promoTitle, $amount) {
            $wallet = Wallet::where('user_id', $user->id)->lockForUpdate()->first();
            
            if (!$wallet || $wallet->balance < $amount) {
                throw new \Exception('Insufficient balance for this purchase.');
            }

            $wallet->balance -= $amount;
            $wallet->save();

            // Create a payment transaction record
            return Transaction::create([
                'user_id' => $user->id,
                'type'    => 'payment',
                'amount'  => $amount,
                'reference_id' => 'PRM_' . $promoId . '_' . time(),
                'description' => 'Voucher - ' . $promoTitle,
                'status'  => 'success',
            ]);
        });
    }
}
?>
