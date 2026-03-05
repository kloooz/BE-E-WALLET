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
                'first_name' => str_replace(' ', '', $user->username), // some names have issues if we don't ensure it
                'email' => $user->email,
                'phone' => $user->phone ?? '081234567890', // Dana requires a phone number
            ],
            'callbacks' => [
                'finish' => 'http://localhost:5173/payment/finish',
                'unfinish' => 'http://localhost:5173/payment/unfinish',
                'error' => 'http://localhost:5173/payment/error',
            ],
        ];

        return Snap::getSnapToken($params);
    }
}
