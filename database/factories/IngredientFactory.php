<?php

namespace Database\Factories;

use App\Models\Ingredient;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Ingredient>
 */
class IngredientFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $ingredients = [
            'Thịt bò', 'Thịt heo', 'Thịt gà', 'Cá hồi', 'Tôm sú', 'Mực tươi', 'Cua đồng',
            'Hành tím', 'Hành lá', 'Tỏi', 'Gừng', 'Sả', 'Ớt đỏ', 'Tiêu đen',
            'Nước mắm', 'Dầu hào', 'Xì dầu', 'Muối', 'Đường', 'Dầu ăn',
            'Cà chua', 'Cà rốt', 'Khoai tây', 'Bắp cải', 'Rau muống', 'Đậu bắp',
            'Nấm rơm', 'Nấm hương', 'Đậu phụ', 'Trứng gà', 'Trứng vịt',
            'Bún tươi', 'Bánh phở', 'Mì trứng', 'Cơm trắng', 'Xôi nếp',
            'Nước cốt dừa', 'Mật ong', 'Giấm', 'Rượu trắng',
        ];
        return [
            'name' => fake()->unique()->randomElement($ingredients),
            'image' => 'https://images.unsplash.com/photo-1596040033229-a9821ebd058d?w=400',
        ];
    }
}
