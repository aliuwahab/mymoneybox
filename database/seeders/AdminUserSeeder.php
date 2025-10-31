<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Country;
use App\Enums\UserType;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AdminUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get Ghana as default country (or first country)
        $country = Country::where('code', 'GH')->first() ?? Country::first();

        $admins = [
            [
                'name' => 'Aliu Wahab Gbeila',
                'email' => 'aliuwahab@gmail.com',
                'password' => Hash::make('password'),
                'user_type' => UserType::Admin->value,
                'country_id' => $country->id,
                'email_verified_at' => now(),
            ],
            [
                'name' => 'Chris-Vincent Agyepong',
                'email' => 'vfebiri@gmail.com',
                'password' => Hash::make('password'),
                'user_type' => UserType::Admin->value,
                'country_id' => $country->id,
                'email_verified_at' => now(),
            ],
        ];

        foreach ($admins as $admin) {
            User::updateOrCreate(
                ['email' => $admin['email']],
                $admin
            );
        }

        $this->command->info('Admin users created successfully!');
    }
}
