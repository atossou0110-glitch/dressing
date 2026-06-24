<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->unsignedInteger('loyalty_points')->default(0)->after('is_admin');
            $table->unsignedBigInteger('loyalty_lifetime_spend')->default(0)->after('loyalty_points');
            $table->string('loyalty_tier', 32)->default('starter')->after('loyalty_lifetime_spend');
            $table->timestamp('last_loyalty_rewarded_at')->nullable()->after('loyalty_tier');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn([
                'loyalty_points',
                'loyalty_lifetime_spend',
                'loyalty_tier',
                'last_loyalty_rewarded_at',
            ]);
        });
    }
};
