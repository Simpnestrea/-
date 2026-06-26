<?php

namespace Database\Factories;

use App\Models\Recipe;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Recipe>
 */
class RecipeFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $titles = [
            'Gà Kho Gừng Đậm Đà', 'Cá Hồi Áp Chảo Sốt Bơ', 'Thịt Kho Tàu Miền Nam',
            'Canh Chua Cá Lóc', 'Mực Xào Sa Tế', 'Tôm Rim Mặn Ngọt', 'Sườn Nướng Mật Ong',
            'Bò Lúc Lắc Phong Cách', 'Vịt Nấu Chao', 'Ếch Xào Sả Ớt', 'Cua Rang Muối',
            'Lẩu Thái Hải Sản', 'Bún Bò Huế Chuẩn Vị', 'Hủ Tiếu Nam Vang', 'Mì Quảng Gà',
            'Chả Cá Lã Vọng', 'Bánh Cuốn Hà Nội', 'Xôi Gấc Đỏ', 'Chè Đậu Xanh Nước Cốt Dừa',
            'Bánh Bèo Chén', 'Bò Nướng Lá Lốt', 'Nem Chua Rán', 'Chả Giò Hải Sản',
            'Lẩu Gà Lá Giang', 'Thịt Heo Quay Da Giòn', 'Bún Chả Hà Nội', 'Bánh Xèo Miền Tây',
            'Cơm Chiên Dương Châu', 'Mì Xào Giòn Hải Sản', 'Lẩu Riêu Cua Đồng',
        ];
        $descriptions = [
            'Món ăn đậm đà hương vị quê hương, được chế biến từ những nguyên liệu tươi ngon nhất.',
            'Công thức truyền thống kết hợp cùng gia vị đặc trưng tạo nên hương vị khó quên.',
            'Một món ngon dễ làm, phù hợp cho bữa cơm gia đình ấm cúng cuối tuần.',
            'Hương vị đặc sắc, thơm lừng từ các loại thảo mộc tươi, ăn một lần là nhớ mãi.',
            'Món ăn bổ dưỡng, giàu dinh dưỡng, thích hợp cho cả trẻ em và người lớn.',
            'Kết hợp hài hòa giữa vị chua, cay, mặn, ngọt – tinh hoa ẩm thực Việt Nam.',
            'Công thức đơn giản nhưng kết quả cực ngon, bạn bạn khen tấm tắc.',
        ];
        $tips = [
            'Ướp gia vị ít nhất 30 phút trước khi nấu để thịt thấm đều.',
            'Nên dùng chảo gang để giữ nhiệt tốt hơn khi xào.',
            'Thêm một ít đường thốt nốt để nước kho ngọt thanh tự nhiên hơn.',
            'Luôn vo gạo thật sạch và nấu với lửa nhỏ để cơm tơi xốp.',
            'Rau sống nên ngâm nước muối loãng 10 phút cho sạch và giòn.',
            'Dùng nước dừa tươi thay nước lọc khi kho thịt sẽ ngon hơn rất nhiều.',
        ];
        $foods = [
            'https://images.unsplash.com/photo-1582878826629-29b7ad1ccd63?w=800',
            'https://images.unsplash.com/photo-1565299585323-38d6b0865b47?w=800',
            'https://images.unsplash.com/photo-1547592180-85f173990554?w=800',
            'https://images.unsplash.com/photo-1512058564366-18510be2db19?w=800',
            'https://images.unsplash.com/photo-1574484284002-952d92456975?w=800',
            'https://images.unsplash.com/photo-1562802378-063ec186a863?w=800',
            'https://images.unsplash.com/photo-1504674900247-0877df9cc836?w=800',
            'https://images.unsplash.com/photo-1559847844-5315695dadae?w=800',
        ];
        $title = fake()->randomElement($titles);

        // Tránh trùng lặp tên món ăn bằng cách thêm hậu tố đặc trưng
        if (\App\Models\Recipe::where('title', $title)->exists()) {
            $suffixes = [
                'Cô Hương', 'Gia Truyền', 'Đặc Biệt', 'Mẹ Làm', 'Nhà Làm', 
                'Đậm Đà', 'Thơm Ngon', 'Vỉa Hè', 'Khánh Ly', 'Bà Ba',
                'Cô Ba', 'Chú Tư', 'Cô Chín', 'Ông Năm', 'Bà Sáu'
            ];
            // Loại bỏ một số đuôi mô tả cũ nếu có để ghép đuôi mới tự nhiên hơn
            $baseTitle = preg_replace('/\s+(Chuẩn Vị|Đậm Đà|Miền Nam|Phong Cách|Da Giòn|Miền Tây|Dương Châu|Hải Sản|Lã Vọng|Đỏ|Nước Cốt Dừa)$/i', '', $title);
            $title = $baseTitle . ' ' . fake()->randomElement($suffixes);

            // Đảm bảo tuyệt đối không trùng lặp
            while (\App\Models\Recipe::where('title', $title)->exists()) {
                $title = $baseTitle . ' ' . fake()->randomElement($suffixes) . ' ' . fake()->numberBetween(2, 9);
            }
        }

        return [
            'user_id' => \App\Models\User::factory(),
            'category_id' => \App\Models\Category::factory(),
            'title' => $title,
            'slug' => \Illuminate\Support\Str::slug($title) . '-' . fake()->numberBetween(1, 9999),
            'description' => fake()->randomElement($descriptions),
            'time_to_cook' => fake()->numberBetween(15, 120),
            'difficulty' => fake()->randomElement(['dễ', 'trung bình', 'khó']),
            'image' => fake()->randomElement($foods),
            'views_count' => fake()->numberBetween(0, 5000),
            'tips' => fake()->randomElement($tips),
            'is_premium' => fake()->boolean(20),
        ];
    }
}
