<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AdminUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Check if admin user already exists
        $adminExists = User::where('email', 'admin@aura.com')->exists();
        
        if (!$adminExists) {
            User::create([
                'uuid' => \Illuminate\Support\Str::uuid(),
                'email' => 'admin@aura.com',
                'phone' => '+1234567890',
                'password' => Hash::make('admin123'),
                'is_active' => true,
                'is_verified' => true,
                'is_premium' => true,
                'role' => 'admin',
            ]);
            
            $this->command->info('Admin user created successfully!');
            $this->command->info('Email: admin@aura.com');
            $this->command->info('Password: admin123');
        } else {
            $this->command->info('Admin user already exists.');
        }
    }
}
