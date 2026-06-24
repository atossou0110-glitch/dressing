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
        Schema::create('flash_campaigns', function (Blueprint $table) {
            $table->id();
            $table->string('title', 120);
            $table->string('message', 255);
            $table->string('discount_code', 40)->nullable();
            $table->string('cta_label', 60)->nullable();
            $table->string('cta_url')->nullable();
            $table->string('audience', 32)->default('all');
            $table->boolean('is_active')->default(true)->index();
            $table->timestamp('starts_at')->nullable()->index();
            $table->timestamp('ends_at')->nullable()->index();
            $table->foreignId('created_by_user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('last_sent_at')->nullable();
            $table->unsignedInteger('impressions_count')->default(0);
            $table->timestamps();
        });

        Schema::create('browser_notification_subscriptions', function (Blueprint $table) {
            $table->id();
            $table->string('visitor_hash', 64)->unique();
            $table->string('email')->nullable()->index();
            $table->string('permission', 20)->default('default')->index();
            $table->timestamp('subscribed_at')->nullable();
            $table->timestamp('last_seen_at')->nullable();
            $table->unsignedBigInteger('last_notified_campaign_id')->nullable();
            $table->timestamp('last_notified_at')->nullable();
            $table->json('meta')->nullable();
            $table->timestamps();

            $table->foreign('last_notified_campaign_id', 'browser_notif_last_campaign_fk')
                ->references('id')
                ->on('flash_campaigns')
                ->nullOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('browser_notification_subscriptions');
        Schema::dropIfExists('flash_campaigns');
    }
};
