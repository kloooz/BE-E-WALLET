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
        Config::$serverKey    = config('services.midtrans.server_key');
        Config::$clientKey    = config('services.midtrans.client_key');
        Config::$isProduction = config('services.midtrans.is_production');
        Config::$isSanitized  = true;
        Config::$is3ds        = true;
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
        // URLs configurable via .env:
        // MIDTRANS_FRONTEND_URL=https://your-frontend.com
        // MIDTRANS_NOTIFICATION_URL=https://your-ngrok.ngrok-free.app/api/webhook/midtrans
        $frontendUrl     = config('services.midtrans.frontend_url', 'http://localhost:5173');
        $notificationUrl = config('services.midtrans.notification_url', config('app.url') . '/api/webhook/midtrans');
        // Backend handles finish redirect: auto-verifies with Midtrans API, updates balance, then redirects to frontend
        $backendUrl      = config('app.url');

        $params = [
            'transaction_details' => [
                'order_id'     => $transaction->reference_id,
                'gross_amount' => $transaction->amount,
            ],
            'customer_details' => [
                'first_name' => str_replace(' ', '', $user->username),
                'email'      => $user->email,
                'phone'      => $user->phone ?? '081234567890',
            ],
            'callbacks' => [
                'finish'   => $backendUrl . '/api/payment/finish',
                'unfinish' => $frontendUrl . '/payment/unfinish',
                'error'    => $frontendUrl . '/payment/error',
            ],
            'notification_url' => $notificationUrl,
            // Explicit payment methods — ensures DANA, GoPay, ShopeePay, VA all appear
            'enabled_payments' => [
                'credit_card', 'bca_va', 'bni_va', 'bri_va', 'permata_va',
                'other_va', 'echannel', 'gopay', 'dana', 'shopeepay',
                'linkaja', 'indomaret', 'akulaku',
            ],
            'custom_expiry' => [
                'expiry_duration' => 60,
                'unit'            => 'minute',
            ],
        ];

        return Snap::getSnapToken($params);
    }
}
