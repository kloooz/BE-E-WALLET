<?php

namespace App\Http\Controllers;

use App\Http\Requests\ScanQrRequest;
use App\Services\WalletService;
use App\Traits\ApiResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use App\Mail\PaymentSuccessMail;
use Illuminate\Support\Facades\Mail;

class QrPaymentController
{
    use ApiResponse;

    protected $walletService;

    public function __construct(WalletService $walletService)
    {
        $this->walletService = $walletService;
    }

    /**
     * Generate a dummy QR code string for testing.
     * The QR code will just be a base64 encoded JSON string.
     */
    public function generateDummyQr(): JsonResponse
    {
        $dummyData = [
            'merchant_id' => 'MCH-12345',
            'merchant_name' => 'Warteg Berkah',
            'amount' => 15000,
            'transaction_id' => 'QR_' . time() . rand(100, 999),
        ];

        $qrString = base64_encode(json_encode($dummyData));

        return $this->success('Dummy QR generated', [
            'qr_string' => $qrString,
            'decoded_data' => $dummyData
        ]);
    }

    /**
     * Process a QR Payment.
     */
    public function scanQrPayment(ScanQrRequest $request): JsonResponse
    {
        $user = Auth::user();
        $qrCode = $request->validated()['qr_code'];
        $pin = $request->validated()['pin'];

        // Verify PIN
        if (!\Illuminate\Support\Facades\Hash::check($pin, $user->pin)) {
            return $this->error('Invalid PIN', null, 401);
        }

        // Decode the QR Code
        $decodedData = json_decode(base64_decode($qrCode), true);

        if (!$decodedData || !isset($decodedData['amount']) || !isset($decodedData['merchant_name'])) {
            return $this->error('Invalid QR Code', null, 400);
        }

        try {
            $transaction = $this->walletService->qrPayment($user, $decodedData);
        } catch (\Exception $e) {
            return $this->error($e->getMessage(), null, 400);
        }

        // Send email in separate try-catch so SMTP timeout doesn't block the response
        try {
            Mail::to($user->email)->send(new PaymentSuccessMail($user, $transaction));
        } catch (\Exception $e) {
            Log::warning('Gagal mengirim email PaymentSuccess: ' . $e->getMessage());
        }

        return $this->success('Payment successful', ['transaction' => $transaction], 200);
    }
}
