<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

class AdminSeeder extends Seeder
{
    public function run(): void
    {
        User::updateOrCreate(
            ['email' => 'admin@diskominfo.go.id'],
            [
                'name' => 'Administrator',
                'email' => 'admin@diskominfo.go.id',
                'password' => bcrypt('admin123'),
                'nik' => '0000000000000001',
                'phone' => '081200000001',
                'role' => 'admin',
                'email_verified_at' => now(),
            ]
        );
    }
}
