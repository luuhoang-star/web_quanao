<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $users = [
            [
                'id' => 1,
                'role_id' => 1, // Admin
                'name' => 'Quản trị viên Hệ thống',
                'email' => 'admin@example.com',
                'phone' => '0901234567',
                'password' => Hash::make('123456'),
                'avatar' => 'https://images.unsplash.com/photo-1534528741775-53994a69daeb?auto=format&fit=crop&w=200&q=80',
                'status' => 'active',
                'created_at' => now()->subMonths(6),
                'updated_at' => now()->subMonths(6),
            ],
            [
                'id' => 2,
                'role_id' => 2, // User
                'name' => 'Nguyễn Văn An',
                'email' => 'user@example.com',
                'phone' => '0987654321',
                'password' => Hash::make('123456'),
                'avatar' => 'https://images.unsplash.com/photo-1507003211169-0a1dd7228f2d?auto=format&fit=crop&w=200&q=80',
                'status' => 'active',
                'created_at' => now()->subMonths(3),
                'updated_at' => now()->subMonths(3),
            ],
            [
                'id' => 3,
                'role_id' => 1, // Admin
                'name' => 'Quản trị viên Atino',
                'email' => 'admin@gmail.com',
                'phone' => '0901234568',
                'password' => Hash::make('123456'),
                'avatar' => null,
                'status' => 'active',
                'created_at' => now()->subMonths(5),
                'updated_at' => now()->subMonths(5),
            ],
            [
                'id' => 4,
                'role_id' => 2, // User
                'name' => 'Trần Thị Mai',
                'email' => 'user@gmail.com',
                'phone' => '0973797151',
                'password' => Hash::make('123456'),
                'avatar' => 'https://images.unsplash.com/photo-1494790108377-be9c29b29330?auto=format&fit=crop&w=200&q=80',
                'status' => 'active',
                'created_at' => now()->subMonths(2),
                'updated_at' => now()->subMonths(2),
            ],
            [
                'id' => 5,
                'role_id' => 2, // User
                'name' => 'Lê Hoàng Nam',
                'email' => 'hoangnam@gmail.com',
                'phone' => '0912345678',
                'password' => Hash::make('123456'),
                'avatar' => 'https://images.unsplash.com/photo-1500648767791-00dcc994a43e?auto=format&fit=crop&w=200&q=80',
                'status' => 'active',
                'created_at' => now()->subMonth(),
                'updated_at' => now()->subMonth(),
            ],
            [
                'id' => 6,
                'role_id' => 2, // User
                'name' => 'Phạm Thu Thảo',
                'email' => 'thuthao@gmail.com',
                'phone' => '0934567890',
                'password' => Hash::make('123456'),
                'avatar' => 'https://images.unsplash.com/photo-1544005313-94ddf0286df2?auto=format&fit=crop&w=200&q=80',
                'status' => 'active',
                'created_at' => now()->subWeeks(3),
                'updated_at' => now()->subWeeks(3),
            ],
        ];

        foreach ($users as $user) {
            DB::table('users')->updateOrInsert(['id' => $user['id']], $user);
        }
    }
}
