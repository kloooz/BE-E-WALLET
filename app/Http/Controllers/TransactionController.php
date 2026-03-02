<?php

namespace App\Http\Controllers;

use App\Services\TransactionService;
use App\Traits\ApiResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;

class TransactionController
{
    use ApiResponse;

    protected $transactionService;

    public function __construct(TransactionService $transactionService)
    {
        $this->transactionService = $transactionService;
    }

    /**
     * Get paginated transaction history for the authenticated user.
     */
    public function index(): JsonResponse
    {
        $user = Auth::user();
        $transactions = $this->transactionService->getUserTransactions($user);
        return $this->success('Transaction history retrieved', $transactions);
    }
}
?>
