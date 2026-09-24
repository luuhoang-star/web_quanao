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
        // 1. Thêm trường Giá vốn (cost_price) và Ngưỡng cảnh báo (low_stock_threshold) vào bảng product_variants
        Schema::table('product_variants', function (Blueprint $table) {
            if (! Schema::hasColumn('product_variants', 'cost_price')) {
                $table->decimal('cost_price', 12, 2)->default(0)->after('price')->comment('Giá vốn nhập hàng');
            }
            if (! Schema::hasColumn('product_variants', 'low_stock_threshold')) {
                $table->integer('low_stock_threshold')->default(5)->after('stock_quantity')->comment('Ngưỡng cảnh báo sắp hết hàng');
            }
        });

        // 2. Tạo bảng Lịch sử biến động tồn kho (stock_movements)
        Schema::create('stock_movements', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_variant_id')->constrained('product_variants')->cascadeOnDelete();
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('type', 30)->comment('Loại giao dịch: import, export, adjustment, order_sale, order_cancel, order_return');
            $table->integer('quantity_change')->comment('Số lượng thay đổi (dương hoặc âm)');
            $table->integer('before_quantity')->default(0)->comment('Tồn kho trước khi đổi');
            $table->integer('after_quantity')->default(0)->comment('Tồn kho sau khi đổi');
            $table->decimal('cost_price', 12, 2)->nullable()->comment('Giá vốn tại thời điểm nhập');
            $table->string('supplier')->nullable()->comment('Nhà cung cấp khi nhập hàng');
            $table->string('reference_code', 100)->nullable()->comment('Mã chứng từ/Mã đơn hàng liên quan');
            $table->text('note')->nullable()->comment('Ghi chú / Lý do điều chỉnh');
            $table->timestamps();

            $table->index(['product_variant_id', 'created_at']);
            $table->index('type');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('stock_movements');

        Schema::table('product_variants', function (Blueprint $table) {
            if (Schema::hasColumn('product_variants', 'cost_price')) {
                $table->dropColumn('cost_price');
            }
            if (Schema::hasColumn('product_variants', 'low_stock_threshold')) {
                $table->dropColumn('low_stock_threshold');
            }
        });
    }
};
