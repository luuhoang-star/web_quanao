<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class UserAddressSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Schema::disableForeignKeyConstraints();
        DB::table('user_addresses')->truncate();
        Schema::enableForeignKeyConstraints();

        $addresses = [
            // User 2: user@example.com (Nguyễn Văn An)
            [
                'user_id' => 2,
                'recipient_name' => 'Nguyễn Văn An',
                'recipient_phone' => '0987654321',
                'province_city' => 'Thành phố Hà Nội',
                'district' => 'Quận Cầu Giấy',
                'ward' => 'Phường Dịch Vọng',
                'street_address' => 'Số 120 Đường Cầu Giấy, Tòa nhà Luxury Tower',
                'is_default' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'user_id' => 2,
                'recipient_name' => 'Nguyễn Văn An (Công ty)',
                'recipient_phone' => '0987654321',
                'province_city' => 'Thành phố Hà Nội',
                'district' => 'Quận Nam Từ Liêm',
                'ward' => 'Phường Mỹ Đình 2',
                'street_address' => 'Tầng 8, Tòa nhà Keangnam Landmark 72',
                'is_default' => false,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            // User 4: user@gmail.com (Trần Thị Mai)
            [
                'user_id' => 4,
                'recipient_name' => 'Trần Thị Mai',
                'recipient_phone' => '0973797151',
                'province_city' => 'Thành phố Hồ Chí Minh',
                'district' => 'Quận 1',
                'ward' => 'Phường Bến Nghé',
                'street_address' => 'Số 45 Đường Lê Duẩn',
                'is_default' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'user_id' => 4,
                'recipient_name' => 'Trần Thị Mai (Nhà riêng)',
                'recipient_phone' => '0973797151',
                'province_city' => 'Thành phố Hồ Chí Minh',
                'district' => 'Quận Bình Thạnh',
                'ward' => 'Phường 22',
                'street_address' => 'Căn hộ Landmark 4, Vinhomes Central Park',
                'is_default' => false,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            // User 5: Lê Hoàng Nam
            [
                'user_id' => 5,
                'recipient_name' => 'Lê Hoàng Nam',
                'recipient_phone' => '0912345678',
                'province_city' => 'Thành phố Đà Nẵng',
                'district' => 'Quận Hải Châu',
                'ward' => 'Phường Thạch Thang',
                'street_address' => 'Số 88 Đường Bạch Đằng',
                'is_default' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            // User 6: Phạm Thu Thảo
            [
                'user_id' => 6,
                'recipient_name' => 'Phạm Thu Thảo',
                'recipient_phone' => '0934567890',
                'province_city' => 'Thành phố Cần Thơ',
                'district' => 'Quận Ninh Kiều',
                'ward' => 'Phường An Phú',
                'street_address' => 'Số 15 Đường Mậu Thân',
                'is_default' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ];

        DB::table('user_addresses')->insert($addresses);
    }
}
