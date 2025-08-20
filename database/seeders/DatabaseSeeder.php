<?php

namespace Database\Seeders;

use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call(RolePermissionSeeder::class);

        // Create admin
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
        User::factory(10)->create()->each(function ($user) {
            $user->assignRole('user');
        });
    }
}
