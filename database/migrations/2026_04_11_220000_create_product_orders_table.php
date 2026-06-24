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
        Schema::create('product_orders', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_id')->constrained()->cascadeOnDelete();
            $table->string('reference', 32)->unique();
            $table->string('provider', 24)->default('fedapay');
            $table->string('payment_method', 32);
            $table->string('status', 24)->default('pending')->index();
            $table->string('provider_transaction_id')->nullable()->index();
            $table->string('provider_reference')->nullable()->index();
            $table->string('provider_payment_method', 32)->nullable();
            $table->string('provider_status', 24)->nullable();
            $table->unsignedBigInteger('amount');
            $table->string('currency', 3)->default('XOF');
            $table->string('customer_first_name', 80);
            $table->string('customer_last_name', 80);
            $table->string('customer_email')->nullable();
            $table->string('customer_phone', 32);
            $table->string('customer_city', 80)->default('Cotonou');
            $table->string('customer_country', 2)->default('BJ');
            $table->string('customer_address')->nullable();
            $table->string('customer_zip_code', 20)->nullable();
            $table->text('notes')->nullable();
            $table->json('provider_payload')->nullable();
            $table->timestamp('payment_initiated_at')->nullable();
            $table->timestamp('paid_at')->nullable();
            $table->timestamp('canceled_at')->nullable();
            $table->timestamp('declined_at')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('product_orders');
    }
};
