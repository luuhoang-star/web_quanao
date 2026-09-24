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
            // Thả FK cũ nếu có (hoặc thả FK mặc định) và đặt lại restrictOnDelete để bảo vệ dữ liệu biến thể
            try {
                $table->dropForeign(['color_id']);
            } catch (Exception $e) {
                // Bỏ qua nếu FK tên khác
            }

            try {
                $table->dropForeign(['size_id']);
            } catch (Exception $e) {
                // Bỏ qua nếu FK tên khác
            }

            $table->foreign('color_id')->references('id')->on('colors')->restrictOnDelete();
            $table->foreign('size_id')->references('id')->on('sizes')->restrictOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('product_variants', function (Blueprint $table) {
            try {
                $table->dropForeign(['color_id']);
                $table->dropForeign(['size_id']);
            } catch (Exception $e) {
                // Bỏ qua nếu FK không tồn tại
            }

            $table->foreign('color_id')->references('id')->on('colors')->cascadeOnDelete();
            $table->foreign('size_id')->references('id')->on('sizes')->cascadeOnDelete();
        });
    }
};
