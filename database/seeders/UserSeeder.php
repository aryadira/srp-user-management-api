<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\UserAuth;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // Membuat data user untuk superadmin
        $userSuperAdmin = User::create([
            'user_role_id' => 1, // Role ID superadmin (pastikan role id 2 adalah superadmin)
            'fullname' => 'Super Administrator', // Nama lengkap superadmin
            'phone' => '081234567891', // Nomor telepon superadmin
            'is_active' => true, // Status aktif
            'is_blocked' => false, // Status blokir
        ]);

        // Membuat data user_auth untuk superadmin
        UserAuth::create([
            'user_id' => $userSuperAdmin->id,
            'username' => 'superadmin', // Username superadmin
            'email' => 'superadmin@example.com', // Email superadmin
            'password' => Hash::make('superadmin123'), // Password superadmin (hashing menggunakan Hash::make)
            'is_verified' => 1, // Mengatur status verifikasi
        ]);

        // Membuat data user untuk admin
        $userAdmin = User::create([
            'user_role_id' => 2, // Role ID admin (pastikan role id 1 adalah admin)
            'fullname' => 'Administrator', // Nama lengkap admin
            'phone' => '081234567890', // Nomor telepon admin
            'is_active' => true, // Status aktif
            'is_blocked' => false, // Status blokir
        ]);

        // Membuat data user_auth untuk admin
        UserAuth::create([
            'user_id' => $userAdmin->id,
            'username' => 'admin123', // Username admin
            'email' => 'admin@example.com', // Email admin
            'password' => Hash::make('admin123'), // Password admin (hashing menggunakan Hash::make)
            'is_verified' => 1, // Mengatur status verifikasi
        ]);
    }
}
