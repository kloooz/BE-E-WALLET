<?php

namespace App\Services;

use App\Models\Transaction;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class TransactionService
{
    /**
     * Get paginated transactions for a user ordered by latest.
     *
     * @param User $user
     * @param int $perPage
     * @return \Illuminate\Pagination\LengthAwarePaginator
     */
    public function getUserTransactions(User $user, int $perPage = 15)
    {
        return Transaction::where('user_id', $user->id)
            ->orderByDesc('created_at')
            ->paginate($perPage);
    }
}
?>
