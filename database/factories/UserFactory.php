<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

/**
 * @extends Factory<User>
 */
class UserFactory extends Factory
{
    /**
     * The current password being used by the factory.
     */
    protected static ?string $password;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $bios = [
            'Yêu ẩm thực, thích chia sẻ những công thức nấu ăn ngon.',
            'Đầu bếp gia đình chuyên nghiệp, thích làm các món bánh.',
            'Thích ăn ngon, thích du lịch và khám phá ẩm thực vùng miền.',
            'Chia sẻ các công thức nấu ăn đơn giản cho sinh viên và người bận rộn.',
            'Nấu ăn là nghệ thuật, người nấu ăn là nghệ sĩ.',
            'Chuyên gia ẩm thực Việt Nam truyền thống và hiện đại.',
        ];

        return [
            'name' => fake()->name(),
            'username' => fake()->unique()->userName(),
            'email' => fake()->unique()->safeEmail(),
            'email_verified_at' => now(),
            'password' => static::$password ??= Hash::make('password'),
            'avatar' => 'https://i.pravatar.cc/150?img=' . fake()->numberBetween(1, 70),
            'bio' => fake()->randomElement($bios),
            'is_premium' => fake()->boolean(20),
            'role' => 'beginner',
            'remember_token' => Str::random(10),
        ];
    }

    /**
     * Indicate that the model's email address should be unverified.
     */
    public function unverified(): static
    {
        return $this->state(fn (array $attributes) => [
            'email_verified_at' => null,
        ]);
    }
}
