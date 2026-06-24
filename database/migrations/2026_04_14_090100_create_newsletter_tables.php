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
        Schema::create('newsletter_subscriptions', function (Blueprint $table) {
            $table->id();
            $table->string('email')->unique();
            $table->string('name', 100)->nullable();
            $table->string('visitor_hash', 64)->nullable()->index();
            $table->foreignId('source_product_id')->nullable()->constrained('products')->nullOnDelete();
            $table->string('source_page', 32)->nullable();
            $table->string('source_path')->nullable();
            $table->string('discount_code', 40)->default('WELCOME10');
            $table->string('status', 24)->default('subscribed')->index();
            $table->timestamp('subscribed_at')->nullable();
            $table->timestamp('marketing_opt_in_at')->nullable();
            $table->timestamp('last_seen_at')->nullable();
            $table->unsignedInteger('conversion_orders_count')->default(0);
            $table->unsignedBigInteger('converted_revenue_total')->default(0);
            $table->timestamp('last_converted_at')->nullable();
            $table->json('meta')->nullable();
            $table->timestamps();
        });

        Schema::create('newsletter_conversions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('newsletter_subscription_id')->constrained()->cascadeOnDelete();
            $table->foreignId('product_order_id')->constrained('product_orders')->cascadeOnDelete()->unique();
            $table->foreignId('product_id')->nullable()->constrained('products')->nullOnDelete();
            $table->unsignedBigInteger('amount');
            $table->string('status', 24)->default('approved');
            $table->timestamp('converted_at');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('newsletter_conversions');
        Schema::dropIfExists('newsletter_subscriptions');
    }
};
