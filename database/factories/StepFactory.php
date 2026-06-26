<?php

namespace Database\Factories;

use App\Models\Step;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Step>
 */
class StepFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $steps = [
            'Rửa sạch nguyên liệu, để ráo nước. Ướp với gia vị gồm muối, tiêu, nước mắm trong 15-20 phút.',
            'Làm nóng dầu ăn trong chảo/nồi ở lửa vừa. Phi thơm tỏi và hành tím đập dập.',
            'Cho nguyên liệu chính vào xào hoặc chiên đến khi chín vàng và dậy mùi thơm.',
            'Nêm nếm lại gia vị cho vừa khẩu vị. Thêm nước nếu cần và đun đến khi đạt độ sánh mong muốn.',
            'Tắt bếp, cho rau thơm và hành lá thái nhỏ lên trên. Trình bày ra đĩa và thưởng thức khi còn nóng.',
            'Trộn đều tất cả nguyên liệu với gia vị đã chuẩn bị. Để thấm trong vòng 10-15 phút.',
            'Đun sôi nồi nước lớn, cho thêm chút muối. Trụng qua rau/mì/bún cho mềm vừa tới.',
            'Xếp nguyên liệu ra đĩa/tô theo thứ tự đẹp mắt. Chan nước sốt hoặc nước dùng vào.',
            'Kiểm tra lại độ chín bằng cách dùng đũa hoặc nĩa chọc vào phần dày nhất của nguyên liệu.',
            'Dọn ra bàn, ăn kèm với cơm trắng nóng hổi hoặc bánh mì tươi. Rắc thêm tiêu và vắt chanh nếu thích.',
        ];
        return [
            'recipe_id' => \App\Models\Recipe::factory(),
            'order' => 1,
            'content' => fake()->randomElement($steps),
            'image' => null,
        ];
    }
}
