<?php

namespace App\Services;

use Midtrans\Config;
use Midtrans\Snap;
use App\Models\Transaction;
use App\Models\User;

class MidtransService
{
    public function __construct()
    {
        // Set Midtrans Configuration
        Config::$serverKey = config('services.midtrans.server_key');
        Config::$clientKey = config('services.midtrans.client_key');
        Config::$isProduction = config('services.midtrans.is_production');
        Config::$isSanitized = true;
        Config::$is3ds = true;
    }

    /**
     * Generate Snap Token.
     *
     * @param Transaction $transaction
     * @param User $user
     * @return string
     */
    public function generateSnapToken(Transaction $transaction, User $user): string
    {
        $params = [
            'transaction_details' => [
                'order_id' => $transaction->reference_id,
                'gross_amount' => $transaction->amount,
            ],
            'customer_details' => [
                'first_name' => $user->name,
                'email' => $user->email,
            ],
        ];

        return Snap::getSnapToken($params);
    }
}
