<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Schema;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database with complete sample data.
     */
    public function run(): void
    {
        Schema::disableForeignKeyConstraints();

        $this->call([
            // 1. Phân quyền & Người dùng
            RoleSeeder::class,
            UserSeeder::class,
            UserAddressSeeder::class,

            // 2. Cấu hình Danh mục, Kích thước, Màu sắc & Liên kết
            CategorySeeder::class,
            SizeSeeder::class,
            ColorSeeder::class,
            CategorySizeSeeder::class,
            CategoryColorSeeder::class,

            // 3. Bộ sưu tập & Banner tiếp thị
            CollectionSeeder::class,
            BannerSeeder::class,

            // 4. Sản phẩm, Thư viện ảnh, Biến thể & Quản lý kho
            ProductSeeder::class,

            // 5. Khuyến mãi, Flash Sale & Nổi bật
            FlashSaleAndFeaturedSeeder::class,
            CouponSeeder::class,

            // 6. Đánh giá & Phản hồi khách hàng
            ReviewSeeder::class,

            // 7. Phong cách thời trang: Lookbooks & Outfits
            LookbookSeeder::class,
            OutfitSeeder::class,

            // 8. Đơn hàng, Thanh toán, Lịch sử giao dịch
            DemoUserOrdersSeeder::class,

            // 9. Giỏ hàng & Danh sách yêu thích mẫu
            CartAndWishlistSeeder::class,
        ]);

        Schema::enableForeignKeyConstraints();
    }
}
