<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Chạy các seeder phụ để tạo tài khoản admin và công thức nổi tiếng Việt Nam
        $this->call([
            AdminAccountSeeder::class,
            FamousRecipeSeeder::class,
        ]);

        // Tạo danh mục
        $categories = \App\Models\Category::factory(5)->create();

        // Tạo nguyên liệu
        $ingredients = \App\Models\Ingredient::factory(20)->create();

        // Tạo người dùng
        $users = \App\Models\User::factory(10)->create();

        // Người dùng chính
        $mainUser = User::where('email', 'test@example.com')->first();
        if (!$mainUser) {
            $mainUser = User::factory()->create([
                'name' => 'Test User',
                'email' => 'test@example.com',
                'is_premium' => true,
            ]);
        } else {
            $mainUser->update(['is_premium' => true]);
        }
        $users->push($mainUser);

        // Tạo công thức nấu ăn
        foreach ($users as $user) {
            \App\Models\Recipe::factory(3)->create([
                'user_id' => $user->id,
                'category_id' => $categories->random()->id,
            ])->each(function ($recipe) use ($users, $ingredients) {
                // Các bước thực hiện
                $stepsCount = rand(3, 6);
                for ($i = 1; $i <= $stepsCount; $i++) {
                    \App\Models\Step::factory()->create([
                        'recipe_id' => $recipe->id,
                        'order' => $i,
                    ]);
                }

                // Đính kèm nguyên liệu
                $recipe->ingredients()->attach(
                    $ingredients->random(rand(2, 5))->pluck('id')->toArray(),
                    ['quantity' => rand(1, 10), 'unit' => 'g']
                );

                // Bình luận
                \App\Models\Comment::factory(rand(0, 3))->create([
                    'recipe_id' => $recipe->id,
                    'user_id' => $users->random()->id,
                ]);

                // Lượt thích & Lưu trữ
                $recipe->likedByUsers()->attach($users->random(rand(0, 5))->pluck('id')->toArray());
                $recipe->savedByUsers()->attach($users->random(rand(0, 3))->pluck('id')->toArray());
            });
        }
    }
}
