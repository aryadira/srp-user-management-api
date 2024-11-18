<?php

namespace Database\Seeders;

use App\Models\UserRole;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class UserRoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $roles = [
            ['role_name' => 'customer'],
            ['role_name' => 'admin'],
            ['role_name' => 'superadmin'],
        ];

        foreach ($roles as $role) {
            UserRole::firstOrCreate($role);
        }
    }
}
