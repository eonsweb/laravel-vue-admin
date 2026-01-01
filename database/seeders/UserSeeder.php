<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

use App\Models\User;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $admin = User::firstOrCreate(
            ['email' => 'admin@admin.com'],
            [
                'name' => 'Admin User',
                'username' => 'admin',
                'password' => bcrypt('password'),
            ]
        );

        // Assign Admin Role to admin
        if (! $admin->hasRole('admin')) {
            $admin->assignRole('admin');
        }

        // Create user users
        User::factory(100)->create()->each(function ($user) {
            $user->assignRole('user');
        });
    }
}
