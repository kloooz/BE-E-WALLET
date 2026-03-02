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
     * Top up the user's wallet by a given amount.
     *
     * @param User $user
     * @param int $amount
     * @return Transaction
     */
    public function topUp(User $user, int $amount): Transaction
    {
        return DB::transaction(function () use ($user, $amount) {
            // Lock the wallet row for update to avoid race conditions
            $wallet = Wallet::where('user_id', $user->id)->lockForUpdate()->first();
            $wallet->balance += $amount;
            $wallet->save();

            // Create a top‑up transaction record
            return Transaction::create([
                'user_id' => $user->id,
                'type'    => 'topup',
                'amount'  => $amount,
                // reference_id not needed for top‑up
                'description' => 'Top up balance',
            ]);
        });
    }
}
?>
