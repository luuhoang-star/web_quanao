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
        Schema::table('product_variants', function (Blueprint $table) {
            try {
                $table->dropForeign(['size_id']);
            } catch (Throwable $e) {
                // Bỏ qua nếu FK chưa tồn tại
            }

            $table->foreign('size_id')
                ->references('id')
                ->on('sizes')
                ->nullOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('product_variants', function (Blueprint $table) {
            try {
                $table->dropForeign(['size_id']);
            } catch (Throwable $e) {
                // Bỏ qua nếu FK chưa tồn tại
            }

            $table->foreign('size_id')
                ->references('id')
                ->on('sizes')
                ->restrictOnDelete();
        });
    }
};
