<?php

namespace Database\Seeders;

use App\Models\Role;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class StaffSeeder extends Seeder
{
    public function run(): void
    {
        $staffRole = Role::where('name', 'staff')->firstOrFail();

        $staffMembers = [
            [
                'name' => 'Nguyễn Minh Anh',
                'email' => 'staff1@velora.com',
                'phone' => '0900000002',
                'position' => 'Nhân viên bán hàng',
            ],
            [
                'name' => 'Trần Khánh Linh',
                'email' => 'staff2@velora.com',
                'phone' => '0900000003',
                'position' => 'Nhân viên đo mắt',
            ],
        ];

        foreach ($staffMembers as $staff) {
            User::updateOrCreate(
                [
                    'email' => $staff['email'],
                ],
                [
                    'role_id' => $staffRole->id,
                    'name' => $staff['name'],
                    'phone' => $staff['phone'],
                    'avatar' => null,
                    'position' => $staff['position'],
                    'is_active' => true,
                    'password' => Hash::make('12345678'),
                ]
            );
        }
    }
}