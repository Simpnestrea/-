<?php

namespace Database\Factories;

use App\Models\Category;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Category>
 */
class CategoryFactory extends Factory
{
    protected static array $usedNames = [];

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $categories = ['Món Kho', 'Món Xào', 'Món Luộc', 'Món Nướng', 'Món Hấp', 'Món Chiên', 'Món Canh', 'Lẩu', 'Món Cuốn', 'Bánh', 'Món Nước', 'Món Cơm'];
        
        $existingSlugs = \App\Models\Category::pluck('slug')->toArray();
        $available = array_filter($categories, function ($name) use ($existingSlugs) {
            $slug = \Illuminate\Support\Str::slug($name);
            return !in_array($slug, $existingSlugs) && !in_array($name, self::$usedNames);
        });
        
        if (empty($available)) {
            $name = 'Món mới ' . fake()->word() . ' ' . fake()->numberBetween(1, 9999);
            $slug = \Illuminate\Support\Str::slug($name);
            // Ensure unique slug if word collision occurs
            while (in_array($slug, $existingSlugs) || in_array($name, self::$usedNames)) {
                $name = 'Món mới ' . fake()->word() . ' ' . fake()->numberBetween(1, 9999);
                $slug = \Illuminate\Support\Str::slug($name);
            }
        } else {
            $name = fake()->randomElement($available);
            $slug = \Illuminate\Support\Str::slug($name);
        }

        self::$usedNames[] = $name;

        return [
            'name' => $name,
            'slug' => $slug,
            'image' => 'https://images.unsplash.com/photo-1546069901-ba9599a7e63c?w=400',
        ];
    }
}
