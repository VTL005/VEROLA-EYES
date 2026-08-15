<?php

namespace Database\Seeders;

use App\Models\Role;
use Illuminate\Database\Seeder;

class RoleSeeder extends Seeder
{
    public function run(): void
    {
        $roles = [
            [
                'name' => 'customer',
                'display_name' => 'Khách hàng',
                'description' => 'Khách hàng sử dụng website để mua kính và sử dụng các dịch vụ.',
                'is_active' => true,
            ],
            [
                'name' => 'staff',
                'display_name' => 'Nhân viên',
                'description' => 'Nhân viên vận hành bán hàng và chăm sóc khách hàng.',
                'is_active' => true,
            ],
            [
                'name' => 'admin',
                'display_name' => 'Quản trị viên',
                'description' => 'Quản trị viên có quyền quản lý toàn bộ hệ thống.',
                'is_active' => true,
            ],
        ];

        foreach ($roles as $role) {
            Role::updateOrCreate(
                [
                    'name' => $role['name'],
                ],
                $role
            );
        }
    }
}