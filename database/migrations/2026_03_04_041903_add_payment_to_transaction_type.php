<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     * Note: MODIFY COLUMN for ENUM is MySQL-specific.
     * SQLite (used in testing) does not support it and has no type enforcement.
     */
    public function up(): void
    {
        if (DB::getDriverName() === 'mysql') {
            DB::statement("ALTER TABLE transactions MODIFY COLUMN type ENUM('topup', 'transfer_in', 'transfer_out', 'payment') NOT NULL");
        }
        // SQLite: no-op — SQLite columns are dynamically typed, no enforcement needed
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (DB::getDriverName() === 'mysql') {
            DB::statement("ALTER TABLE transactions MODIFY COLUMN type ENUM('topup', 'transfer_in', 'transfer_out') NOT NULL");
        }
    }
};
