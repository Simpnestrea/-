<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class AdminAccountSeeder extends Seeder
{
    /**
     * Tạo các tài khoản quan trọng.
     * Dùng firstOrCreate nên chạy bao nhiêu lần cũng không bị trùng.
     */
    public function run(): void
    {
        // ===== TÀI KHOẢN ADMIN =====
        User::updateOrCreate(
            ['username' => 'kiet'],
            [
                'name'       => 'Kiệt',
                'email'      => 'kiet@admin.com',
                'password'   => Hash::make('1'),
                'is_premium' => true,
                'is_admin'   => true,
                'balance'    => 1000000.00,
            ]
        );

                // ===== TÀI KHOẢN TEST MẶC ĐỊNH =====
        User::updateOrCreate(
            ['email' => 'test@example.com'],
            [
                'name'       => 'Test User',
                'username'   => 'testuser',
                'password'   => Hash::make('password'),
                'is_premium' => false,
                'balance'    => 500000.00,
            ]
        );

        // ===== TÀI KHOẢN HAHA =====
        User::updateOrCreate(
            ['username' => 'haha'],
            [
                'name'       => 'Haha User',
                'email'      => 'haha@example.com',
                'password'   => Hash::make('password'),
                'is_premium' => false,
                'balance'    => 200000.00,
            ]
        );

        // ===== TÀI KHOẢN MASTER CHEF: GORDON RAMSAY =====
        User::updateOrCreate(
            ['username' => 'gordon'],
            [
                'name'       => 'Gordon Ramsay',
                'email'      => 'gordon@chef.com',
                'password'   => Hash::make('1'),
                'is_premium' => true,
                'is_admin'   => false,
                'balance'    => 0.00,
            ]
        );

        // ===== TÀI KHOẢN MASTER CHEF: CHRISTINE HÀ =====
        User::updateOrCreate(
            ['username' => 'christine'],
            [
                'name'       => 'Christine Hà',
                'email'      => 'christine@chef.com',
                'password'   => Hash::make('1'),
                'is_premium' => true,
                'is_admin'   => false,
                'balance'    => 0.00,
            ]
        );

        // ===== THÊM TÀI KHOẢN KHÁC TẠI ĐÂY NẾU CẦN =====
        // User::firstOrCreate(
        //     ['username' => 'tentaikhoan'],
        //     [
        //         'name'     => 'Tên Hiển Thị',
        //         'email'    => 'email@example.com',
        //         'password' => Hash::make('matkhau'),
        //     ]
        // );
    }
}
