<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     * Changes reference_id from BIGINT to VARCHAR(255) to support UUID/string refs.
     * MySQL-only: SQLite is dynamically typed, column already accepts strings.
     */
    public function up(): void
    {
        if (DB::getDriverName() === 'mysql') {
            DB::statement('ALTER TABLE transactions MODIFY reference_id VARCHAR(255) NULL;');
        }
        // SQLite: no-op — already stores strings without type enforcement
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (DB::getDriverName() === 'mysql') {
            DB::statement('ALTER TABLE transactions MODIFY reference_id BIGINT UNSIGNED NULL;');
        }
    }
};
