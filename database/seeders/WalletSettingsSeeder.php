<?php

namespace Database\Seeders;

use App\Models\WalletSettings;
use Illuminate\Database\Seeder;

class WalletSettingsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $settings = [
            [
                'key' => 'signup_bonus_points',
                'value' => 50,
                'description' => 'Bonus points given to new users upon registration',
            ],
            [
                'key' => 'referral_bonus_points',
                'value' => 25,
                'description' => 'Bonus points given for referring a friend',
            ],
            [
                'key' => 'daily_login_bonus',
                'value' => 5,
                'description' => 'Points given for daily login',
            ],
            [
                'key' => 'super_like_cost',
                'value' => 5,
                'description' => 'Cost of a super like in points',
            ],
            [
                'key' => 'boost_cost',
                'value' => 20,
                'description' => 'Cost of profile boost in points',
            ],
            [
                'key' => 'message_cost',
                'value' => 0,
                'description' => 'Cost to send a message (0 = free)',
            ],
            [
                'key' => 'premium_monthly_cost',
                'value' => 999,
                'description' => 'Monthly premium subscription cost in points',
            ],
        ];

        foreach ($settings as $setting) {
            WalletSettings::updateOrCreate(
                ['key' => $setting['key']],
                [
                    'value' => $setting['value'],
                    'description' => $setting['description'],
                ]
            );
        }

        $this->command->info('Wallet settings seeded successfully!');
    }
}
