<?php

namespace Database\Seeders;

use App\Models\Product;
use App\Models\Review;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class ReviewSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Schema::disableForeignKeyConstraints();
        DB::table('review_images')->truncate();
        DB::table('reviews')->truncate();
        Schema::enableForeignKeyConstraints();

        $products = Product::where('is_active', true)->get();
        if ($products->isEmpty()) {
            return;
        }

        $sampleReviews = [
            [
                'name' => 'Nguyễn Tuấn Anh',
                'email' => 'tuananh@example.com',
                'avatar' => 'https://images.unsplash.com/photo-1534528741775-53994a69daeb?auto=format&fit=crop&w=150&q=80',
                'rating' => 5,
                'comment' => 'Chất lượng quần áo rất tốt, form dáng chuẩn đẹp như mô tả. Giao hàng cực kỳ nhanh chóng chỉ sau 1 ngày đặt. Sẽ ủng hộ shop dài dài!',
                'images' => ['products/ao-thun-nam-1.jpg'],
            ],
            [
                'name' => 'Trần Phương Linh',
                'email' => 'phuonglinh@example.com',
                'avatar' => 'https://images.unsplash.com/photo-1517841905240-472988babdf9?auto=format&fit=crop&w=150&q=80',
                'rating' => 5,
                'comment' => 'Đã mua trọn bộ đồ ngủ Pijama lụa, vải lụa mềm mượt mát lạnh chạm vào thích mê. Áo sơ mi công sở mặc rất vừa vặn và lịch sự.',
                'images' => ['products/pajama-lua-1.jpg'],
            ],
            [
                'name' => 'Lê Thị Mai',
                'email' => 'thimai@example.com',
                'avatar' => 'https://images.unsplash.com/photo-1507003211169-0a1dd7228f2d?auto=format&fit=crop&w=150&q=80',
                'rating' => 5,
                'comment' => 'Giá cả hợp lý nhiều mẫu mã đẹp bắt trend. Đóng gói hộp rất cẩn thận, thơm nức mùi nước hoa sang xịn.',
                'images' => ['products/dam-xoe-nu-1.jpg'],
            ],
            [
                'name' => 'Phạm Quốc Huy',
                'email' => 'quochuy@example.com',
                'avatar' => 'https://images.unsplash.com/photo-1500648767791-00dcc994a43e?auto=format&fit=crop&w=150&q=80',
                'rating' => 5,
                'comment' => 'Sản phẩm đẹp vượt kỳ vọng. Áo thun co giãn tốt, mặc đi tập gym hay đi dạo phố đều rất thoải mái và thoáng mồ hôi.',
                'images' => [],
            ],
            [
                'name' => 'Hoàng Thanh Hằng',
                'email' => 'thanhhang@example.com',
                'avatar' => 'https://images.unsplash.com/photo-1544005313-94ddf0286df2?auto=format&fit=crop&w=150&q=80',
                'rating' => 5,
                'comment' => 'Vải mềm mịn, không hề bị xù lông sau khi giặt máy. Màu sắc ở ngoài còn đẹp hơn trong hình nữa!',
                'images' => ['products/ao-khoac-nu-1.jpg'],
            ],
            [
                'name' => 'Vũ Minh Trí',
                'email' => 'minhtri@example.com',
                'avatar' => 'https://images.unsplash.com/photo-1492562080023-ab3db95bfbce?auto=format&fit=crop&w=150&q=80',
                'rating' => 5,
                'comment' => 'Áo sơ mi slimfit mặc rất tôn dáng, vải chống nhăn giúp mình tiết kiệm nhiều thời gian là ủi mỗi sáng đi làm.',
                'images' => ['products/ao-so-mi-nam-1.jpg'],
            ],
            [
                'name' => 'Đỗ Hải Đăng',
                'email' => 'haidang@example.com',
                'avatar' => 'https://images.unsplash.com/photo-1522075469751-3a6694fb2f61?auto=format&fit=crop&w=150&q=80',
                'rating' => 5,
                'comment' => 'Quần jean 501 dáng đứng rất đẹp, vải dày dặn đường may tỉ mỉ chắc chắn. Rất hài lòng về chất lượng của shop.',
                'images' => ['products/quan-jean-nam-1.jpg'],
            ],
            [
                'name' => 'Ngô Bảo Châu',
                'email' => 'baochau@example.com',
                'avatar' => 'https://images.unsplash.com/photo-1535713875002-d1d0cf377fde?auto=format&fit=crop&w=150&q=80',
                'rating' => 4,
                'comment' => 'Chân váy dáng dài mặc rất xinh và che khuyết điểm tốt, giao hàng hơi trễ nửa ngày do mưa bão nhưng bù lại hàng siêu ưng ý!',
                'images' => [],
            ],
            [
                'name' => 'Bùi Thu Trang',
                'email' => 'thutrang@example.com',
                'avatar' => 'https://images.unsplash.com/photo-1517841905240-472988babdf9?auto=format&fit=crop&w=150&q=80',
                'rating' => 5,
                'comment' => 'Áo thun AIRism mặc mát rượi luôn, đi nắng hè không thấy bí bách khó chịu. Đã giới thiệu cho bạn bè cùng mua.',
                'images' => ['products/ao-thun-nu-1.jpg'],
            ],
            [
                'name' => 'Nguyễn Đình Trọng',
                'email' => 'dinhtrong@example.com',
                'avatar' => 'https://images.unsplash.com/photo-1507003211169-0a1dd7228f2d?auto=format&fit=crop&w=150&q=80',
                'rating' => 5,
                'comment' => 'Thắt lưng da bò thật cầm chắc tay, khóa kim tự động rất nhạy và êm. Rất sang trọng khi diện đồ công sở.',
                'images' => ['products/that-lung-1.jpg'],
            ],
            [
                'name' => 'Lý Gia Hân',
                'email' => 'giahan@example.com',
                'avatar' => 'https://images.unsplash.com/photo-1494790108377-be9c29b29330?auto=format&fit=crop&w=150&q=80',
                'rating' => 5,
                'comment' => 'Váy ngủ lụa quyến rũ dã man, chất lụa cao cấp bóng đẹp mềm mịn. Chồng khen tấm tắc luôn!',
                'images' => [],
            ],
            [
                'name' => 'Dương Quốc Anh',
                'email' => 'quocanh@example.com',
                'avatar' => 'https://images.unsplash.com/photo-1500648767791-00dcc994a43e?auto=format&fit=crop&w=150&q=80',
                'rating' => 4,
                'comment' => 'Balo thể thao đựng laptop vừa vặn êm ái, chống nước ổn định. Rất thích hợp cho người hay di chuyển như mình.',
                'images' => ['products/balo-the-thao-1.jpg'],
            ],
            [
                'name' => 'Trịnh Thúy Vy',
                'email' => 'thuyvy@example.com',
                'avatar' => 'https://images.unsplash.com/photo-1544005313-94ddf0286df2?auto=format&fit=crop&w=150&q=80',
                'rating' => 5,
                'comment' => 'Set blazer nữ công sở cực phẩm luôn ạ, may hai lớp cẩn thận, đệm vai vừa vặn không bị cứng nhắc. Mặc lên nhìn thần thái hẳn!',
                'images' => ['products/set-blazer-nu-1.jpg'],
            ],
            [
                'name' => 'Trần Văn Hùng',
                'email' => 'vanhung@example.com',
                'avatar' => 'https://images.unsplash.com/photo-1492562080023-ab3db95bfbce?auto=format&fit=crop&w=150&q=80',
                'rating' => 5,
                'comment' => 'Set boxer sợi tre mặc cực kỳ thoáng và êm, cạp thun co giãn không để lại vết hằn trên da bụng. Mua lần thứ 2 rồi.',
                'images' => [],
            ],
            [
                'name' => 'Phan Minh Quân',
                'email' => 'minhquan@example.com',
                'avatar' => 'https://images.unsplash.com/photo-1522075469751-3a6694fb2f61?auto=format&fit=crop&w=150&q=80',
                'rating' => 5,
                'comment' => 'Áo polo Atino form dáng đẹp chuẩn mực, cổ áo đứng phom không bị quăn mép khi giặt máy. Vote shop 5 sao tuyệt đối!',
                'images' => ['products/ao-polo-nam-1.jpg'],
            ],
        ];

        foreach ($sampleReviews as $index => $item) {
            $user = User::firstOrCreate(
                ['email' => $item['email']],
                [
                    'role_id' => 2,
                    'name' => $item['name'],
                    'phone' => '09'.rand(10000000, 99999999),
                    'password' => bcrypt('123456'),
                    'avatar' => $item['avatar'],
                    'status' => 'active',
                ]
            );

            $product = $products[$index % $products->count()];

            $review = Review::create([
                'user_id' => $user->id,
                'product_id' => $product->id,
                'rating' => $item['rating'],
                'comment' => $item['comment'],
                'is_approved' => true,
                'created_at' => now()->subDays(rand(1, 20)),
                'updated_at' => now()->subDays(rand(1, 20)),
            ]);

            if (! empty($item['images'])) {
                foreach ($item['images'] as $img) {
                    DB::table('review_images')->insert([
                        'review_id' => $review->id,
                        'image_url' => $img,
                        'created_at' => $review->created_at,
                    ]);
                }
            }
        }
    }
}
