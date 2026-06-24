<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->string('slug')->unique();
            $table->string('code', 1)->unique();
            $table->string('name');
            $table->unsignedInteger('vote_count')->default(0);
            $table->unsignedInteger('preorder_count')->default(0);
            $table->timestamps();
        });

        Schema::create('product_reviews', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_id')->constrained()->cascadeOnDelete();
            $table->string('author_name', 80);
            $table->text('body');
            $table->unsignedTinyInteger('rating');
            $table->timestamps();
        });

        $timestamp = now();

        DB::table('products')->insert([
            [
                'slug' => 'produit-a',
                'code' => 'A',
                'name' => 'commode',
                'vote_count' => 58,
                'preorder_count' => 34,
                'created_at' => $timestamp,
                'updated_at' => $timestamp,
            ],
            [
                'slug' => 'produit-b',
                'code' => 'B',
                'name' => 'Meuble tiroirs empilables',
                'vote_count' => 42,
                'preorder_count' => 18,
                'created_at' => $timestamp,
                'updated_at' => $timestamp,
            ],
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('product_reviews');
        Schema::dropIfExists('products');
    }
};
