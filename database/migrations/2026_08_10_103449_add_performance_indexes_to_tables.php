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
            $table->index(['is_active', 'created_at']);
            $table->index(['is_active', 'category_id']);
            $table->index('price');
            $table->index('sale_price');
        });

        Schema::table('product_variants', function (Blueprint $table) {
            $table->index(['product_id', 'is_active']);
            $table->index(['size_id', 'color_id']);
            $table->index('stock_quantity');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->dropIndex(['is_active', 'created_at']);
            $table->dropIndex(['is_active', 'category_id']);
            $table->dropIndex(['price']);
            $table->dropIndex(['sale_price']);
        });

        Schema::table('product_variants', function (Blueprint $table) {
            $table->dropIndex(['product_id', 'is_active']);
            $table->dropIndex(['size_id', 'color_id']);
            $table->dropIndex(['stock_quantity']);
        });
    }
};
