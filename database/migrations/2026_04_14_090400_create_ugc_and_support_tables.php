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
        Schema::create('ugc_submissions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_id')->nullable()->constrained('products')->nullOnDelete();
            $table->string('author_name', 80);
            $table->string('author_city', 80)->nullable();
            $table->string('author_email')->nullable();
            $table->string('caption', 255);
            $table->string('photo_path');
            $table->string('status', 24)->default('pending')->index();
            $table->string('visitor_hash', 64)->nullable()->index();
            $table->string('ip_hash', 64)->nullable();
            $table->timestamp('approved_at')->nullable();
            $table->timestamp('rejected_at')->nullable();
            $table->timestamp('featured_at')->nullable();
            $table->text('admin_notes')->nullable();
            $table->timestamps();
        });

        Schema::create('support_conversations', function (Blueprint $table) {
            $table->id();
            $table->string('visitor_hash', 64)->unique();
            $table->foreignId('source_product_id')->nullable()->constrained('products')->nullOnDelete();
            $table->string('customer_name', 80)->nullable();
            $table->string('customer_email')->nullable();
            $table->string('source_path')->nullable();
            $table->string('status', 24)->default('open')->index();
            $table->boolean('needs_human')->default(false);
            $table->timestamp('last_message_at')->nullable();
            $table->timestamp('last_user_message_at')->nullable();
            $table->timestamp('last_assistant_message_at')->nullable();
            $table->json('meta')->nullable();
            $table->timestamps();
        });

        Schema::create('support_messages', function (Blueprint $table) {
            $table->id();
            $table->foreignId('support_conversation_id')->constrained()->cascadeOnDelete();
            $table->string('role', 20);
            $table->text('body');
            $table->decimal('confidence', 4, 2)->nullable();
            $table->json('meta')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('support_messages');
        Schema::dropIfExists('support_conversations');
        Schema::dropIfExists('ugc_submissions');
    }
};
