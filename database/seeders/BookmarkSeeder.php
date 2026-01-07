<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

use App\Models\Post;
use App\Models\User;

class BookmarkSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $users = User::all();

        Post::all()->each(function ($post) use ($users) {
            $post->bookmarkedBy()->createMany(
                $users->random(rand(0, 5))
                    ->map(fn($user)
                    => ['user_id' => $user->id])->toArray()
            );
        });
    }
}
