<?php

namespace App\Http\Controllers;

use App\Http\Requests\TopUpRequest;
use App\Http\Requests\TransferRequest;
use App\Services\WalletService;
use App\Services\TransferService;
use App\Traits\ApiResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;

class WalletController
{
    use ApiResponse;

    protected $walletService;
    protected $transferService;

    public function __construct(WalletService $walletService, TransferService $transferService)
    {
        $this->walletService = $walletService;
        $this->transferService = $transferService;
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
     * Top up wallet balance.
     */
    public function topUp(TopUpRequest $request): JsonResponse
    {
        $user = Auth::user();
        $amount = (int) $request->validated()['amount'];
        $transaction = $this->walletService->topUp($user, $amount);
        return $this->success('Top up successful', ['transaction' => $transaction], 201);
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
        try {
            $result = $this->transferService->transfer($user, $identifier, $amount);
            return $this->success('Transfer successful', $result, 201);
        } catch (\Exception $e) {
            return $this->error($e->getMessage(), null, 400);
        }
    }
}
?>
