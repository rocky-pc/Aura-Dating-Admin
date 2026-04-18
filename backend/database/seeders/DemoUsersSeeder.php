<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\UserProfile;
use App\Models\ProfileImage;
use App\Models\Wallet;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DemoUsersSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $users = [
            [
                'first_name' => 'Sarah',
                'last_name' => 'Johnson',
                'email' => 'sarah.johnson@demo.com',
                'phone' => '+1234567891',
                'password' => 'demo123456',
                'gender' => 'female',
                'date_of_birth' => '1998-05-15',
                'bio' => 'Coffee lover ☕ | Travel enthusiast ✈️ | Dog mom 🐕 Looking for someone to explore the world with!',
                'job_title' => 'Marketing Manager',
                'city' => 'New York',
                'country' => 'USA',
                'image_url' => 'https://images.unsplash.com/photo-1494790108377-be9c29b29330?w=400&h=400&fit=crop&crop=face',
            ],
            [
                'first_name' => 'Michael',
                'last_name' => 'Chen',
                'email' => 'michael.chen@demo.com',
                'phone' => '+1234567892',
                'password' => 'demo123456',
                'gender' => 'male',
                'date_of_birth' => '1995-08-22',
                'bio' => 'Software Developer 💻 | Fitness buff 🏋️ | Foodie 🍕 Let\'s grab a bite and see where it goes!',
                'job_title' => 'Senior Developer',
                'city' => 'San Francisco',
                'country' => 'USA',
                'image_url' => 'https://images.unsplash.com/photo-1507003211169-0a1dd7228f2d?w=400&h=400&fit=crop&crop=face',
            ],
            [
                'first_name' => 'Emma',
                'last_name' => 'Williams',
                'email' => 'emma.williams@demo.com',
                'phone' => '+1234567893',
                'password' => 'demo123456',
                'gender' => 'female',
                'date_of_birth' => '2000-03-10',
                'bio' => 'Artist 🎨 | Music lover 🎵 | Adventure seeker 🏔️ swipe right if you can recommend a good book!',
                'job_title' => 'Graphic Designer',
                'city' => 'Los Angeles',
                'country' => 'USA',
                'image_url' => 'https://images.unsplash.com/photo-1438761681033-6461ffad8d80?w=400&h=400&fit=crop&crop=face',
            ],
            [
                'first_name' => 'David',
                'last_name' => 'Martinez',
                'email' => 'david.martinez@demo.com',
                'phone' => '+1234567894',
                'password' => 'demo123456',
                'gender' => 'male',
                'date_of_birth' => '1992-11-28',
                'bio' => 'Entrepreneur 🚀 | Sports fan ⚽ | Looking for something real ❤️ Success is nothing without someone to share it with.',
                'job_title' => 'Business Owner',
                'city' => 'Miami',
                'country' => 'USA',
                'image_url' => 'https://images.unsplash.com/photo-1500648767791-00dcc994a43e?w=400&h=400&fit=crop&crop=face',
            ],
        ];

        foreach ($users as $userData) {
            // Check if user already exists
            $exists = User::where('email', $userData['email'])->exists();
            
            if (!$exists) {
                // Create user
                $user = User::create([
                    'uuid' => \Illuminate\Support\Str::uuid(),
                    'email' => $userData['email'],
                    'phone' => $userData['phone'],
                    'password' => Hash::make($userData['password']),
                    'is_active' => true,
                    'is_verified' => true,
                    'is_premium' => true,
                    'role' => 'user',
                    'last_active_at' => now(),
                ]);

                // Create user profile
                $profile = UserProfile::create([
                    'user_id' => $user->id,
                    'first_name' => $userData['first_name'],
                    'last_name' => $userData['last_name'],
                    'date_of_birth' => $userData['date_of_birth'],
                    'gender' => $userData['gender'],
                    'interested_in' => 'everyone',
                    'bio' => $userData['bio'],
                    'latitude' => $this->getRandomLat($userData['city']),
                    'longitude' => $this->getRandomLng($userData['city']),
                    'location_updated_at' => now(),
                    'max_distance' => 50,
                    'min_age' => 18,
                    'max_age' => 50,
                    'profile_completed' => true,
                ]);

                // Create profile image
                ProfileImage::create([
                    'user_id' => $user->id,
                    'image_url' => $userData['image_url'],
                    'is_primary' => true,
                    'is_verified' => true,
                    'order' => 0,
                ]);

                // Create wallet for user
                Wallet::create([
                    'user_id' => $user->id,
                    'balance' => 100, // Give demo users some starting points
                    'bonus_points' => 50,
                    'total_spent' => 0,
                    'lifetime_earnings' => 0,
                ]);

                $this->command->info("Created demo user: {$userData['first_name']} {$userData['last_name']}");
            } else {
                $this->command->info("Demo user {$userData['email']} already exists.");
            }
        }
        
        $this->command->info('Demo users seeded successfully!');
    }
    
    private function getRandomLat($city): float
    {
        $coords = [
            'New York' => 40.7128,
            'San Francisco' => 37.7749,
            'Los Angeles' => 34.0522,
            'Miami' => 25.7617,
        ];
        
        $baseLat = $coords[$city] ?? 40.7128;
        return $baseLat + (mt_rand(-100, 100) / 1000);
    }
    
    private function getRandomLng($city): float
    {
        $coords = [
            'New York' => -74.0060,
            'San Francisco' => -122.4194,
            'Los Angeles' => -118.2437,
            'Miami' => -80.1918,
        ];
        
        $baseLng = $coords[$city] ?? -74.0060;
        return $baseLng + (mt_rand(-100, 100) / 1000);
    }
}
