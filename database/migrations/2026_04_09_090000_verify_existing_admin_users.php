<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        DB::table('users')
            ->where('is_admin', true)
            ->whereNull('email_verified_at')
            ->update([
                'email_verified_at' => now(),
                'updated_at' => now(),
            ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Irreversible data backfill: keep verified timestamps intact on rollback.
    }
};
