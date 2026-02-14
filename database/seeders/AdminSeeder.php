<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class AdminSeeder extends Seeder
{
    public function run()
    {
        User::create([
            'name' => 'Admin',
            'email' => 'admin@arifah.gym',
            'password' => Hash::make('admin123'),
            'role' => 'super_admin',
        ]);

        echo "✅ User Admin berhasil dibuat!\n";
        echo "Email: admin@arifah.gym\n";
        echo "Password: admin123\n";
        echo "Role: super_admin\n";
    }
}
