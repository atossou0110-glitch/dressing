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
        Schema::table('products', function (Blueprint $table) {
            // Si les colonnes n'existent pas déjà, les ajouter
            if (!Schema::hasColumn('products', 'category')) {
                $table->string('category')->nullable()->after('name');
            }
            if (!Schema::hasColumn('products', 'description')) {
                $table->text('description')->nullable()->after('category');
            }
            if (!Schema::hasColumn('products', 'short_description')) {
                $table->string('short_description')->nullable()->after('description');
            }
            if (!Schema::hasColumn('products', 'price')) {
                $table->unsignedBigInteger('price')->nullable()->after('short_description');
            }
            if (!Schema::hasColumn('products', 'discount_price')) {
                $table->unsignedBigInteger('discount_price')->nullable()->after('price');
            }
            if (!Schema::hasColumn('products', 'stock_quantity')) {
                $table->unsignedInteger('stock_quantity')->default(0)->after('discount_price');
            }
            if (!Schema::hasColumn('products', 'sku')) {
                $table->string('sku')->nullable()->unique()->after('stock_quantity');
            }
            if (!Schema::hasColumn('products', 'weight')) {
                $table->decimal('weight', 8, 2)->nullable()->after('sku');
            }
            if (!Schema::hasColumn('products', 'bestseller')) {
                $table->boolean('bestseller')->default(false)->after('weight');
            }
            if (!Schema::hasColumn('products', 'featured')) {
                $table->boolean('featured')->default(false)->after('bestseller');
            }
            if (!Schema::hasColumn('products', 'images')) {
                $table->json('images')->nullable()->after('featured');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $columns = [
                'category', 'description', 'short_description', 'price', 'discount_price',
                'stock_quantity', 'sku', 'weight', 'bestseller', 'featured', 'images'
            ];
            
            foreach ($columns as $column) {
                if (Schema::hasColumn('products', $column)) {
                    $table->dropColumn($column);
                }
            }
        });
    }
};
