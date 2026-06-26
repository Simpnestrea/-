<?php

namespace Database\Factories;

use App\Models\Comment;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Comment>
 */
class CommentFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $comments = [
            'Món này ngon quá, mình sẽ làm thử cho cả nhà ăn!',
            'Công thức chi tiết, rất dễ hiểu, cảm ơn tác giả nhé!',
            'Mình đã làm thử và thành công ngay từ lần đầu tiên.',
            'Có cần phải thêm nước dừa không bạn nhỉ?',
            'Món ăn trông hấp dẫn quá, màu sắc rất đẹp mắt.',
            'Nhà mình ai cũng khen ngon khi mình nấu theo công thức này.',
            'Bí quyết rất hữu ích, cảm ơn bạn đã chia sẻ.',
            'Công thức tuyệt vời, lưu lại để nấu dịp cuối tuần.',
            'Nhìn thôi đã thấy thèm rồi, tối nay phải triển khai ngay.',
            'Món này ăn kèm với cơm nóng là chuẩn vị nhất luôn.',
        ];

        return [
            'user_id' => \App\Models\User::factory(),
            'recipe_id' => \App\Models\Recipe::factory(),
            'content' => fake()->randomElement($comments),
        ];
    }
}
