<?php

namespace Database\Seeders;

use App\Models\Role;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AdminSeeder extends Seeder
{
    public function run(): void
    {
        $adminRole = Role::where('name', 'admin')->firstOrFail();

        User::updateOrCreate(
            [
                'email' => 'admin@velora.com',
            ],
            [
                'role_id' => $adminRole->id,
                'name' => 'VELORA Admin',
                'phone' => '0900000001',
                'avatar' => null,
                'position' => 'Quản trị hệ thống',
                'is_active' => true,
                'password' => Hash::make('12345678'),
            ]
        );
    }
}