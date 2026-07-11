<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AdminSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create or update admin user
        User::updateOrCreate(
            ['email' => 'admin@dukarahisi.com'],
            [
                'name' => 'Dukarahisi Admin',
                'email' => 'admin@dukarahisi.com',
                'password' => Hash::make('Admin@123456'),
                'role' => 'admin',
            ]
        );

        $this->command->info('✅ Admin user created successfully!');
        $this->command->info('📧 Email: admin@dukarahisi.com');
        $this->command->info('🔑 Password: Admin@123456');
        $this->command->warn('⚠️  Please change the password after first login!');
    }
}
