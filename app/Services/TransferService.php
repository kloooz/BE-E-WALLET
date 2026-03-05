<?php

namespace App\Services;

use Illuminate\Support\Str;

use App\Models\User;
use App\Models\Wallet;
use App\Models\Transaction;
use Illuminate\Support\Facades\DB;
use Exception;

class TransferService
{
    /**
     * Transfer amount from sender to receiver identified by email or phone.
     *
     * @param User   $sender     Authenticated user sending the money
     * @param string $identifier Email or phone of the receiver
     * @param int    $amount    Amount to transfer (must be >= 1)
     * @return array  Contains the two Transaction models (outgoing and incoming)
     * @throws Exception When validation fails (insufficient balance, self transfer, receiver not found)
     */
    public function transfer(User $sender, string $identifier, int $amount): array
    {
        // Prevent self-transfer
        if ($sender->email === $identifier || $sender->phone === $identifier || $sender->username === $identifier) {
            throw new Exception('Cannot transfer to self');
        }

        // Find receiver by email, phone, or username
        $receiver = User::where('email', $identifier)
            ->orWhere('phone', $identifier)
            ->orWhere('username', $identifier)
            ->first();

        if (!$receiver) {
            throw new Exception('Receiver not found');
        }

        return DB::transaction(function () use ($sender, $receiver, $amount, $identifier) {
            // Lock both wallets for update to avoid race conditions
            $senderWallet = Wallet::where('user_id', $sender->id)->lockForUpdate()->first();
            $receiverWallet = Wallet::where('user_id', $receiver->id)->lockForUpdate()->first();

            if ($senderWallet->balance < $amount) {
                // Throw exception to trigger rollback
                throw new Exception('Saldo tidak cukup');
            }

            // Deduct from sender
            $senderWallet->balance -= $amount;
            $senderWallet->save();

            // Add to receiver
            $receiverWallet->balance += $amount;
            $receiverWallet->save();

            // Create a common reference id (use UUID)
            $referenceId = (string) \Str::uuid();

            // Outgoing transaction for sender
            $outgoing = Transaction::create([
                'user_id'      => $sender->id,
                'type'         => 'transfer_out',
                'amount'       => $amount,
                'reference_id' => $referenceId,
                'description'  => 'Transfer to ' . $identifier,
            ]);

            // Incoming transaction for receiver
            $incoming = Transaction::create([
                'user_id'      => $receiver->id,
                'type'         => 'transfer_in',
                'amount'       => $amount,
                'reference_id' => $referenceId,
                'description'  => 'Transfer from ' . $sender->email,
            ]);

            return ['outgoing' => $outgoing, 'incoming' => $incoming];
        });
    }
}
?>
