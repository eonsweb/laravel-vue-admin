<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Post;
use App\Models\User;
use App\Models\Like;



class LikeSeeder extends Seeder
{

    public function run(): void
    {
        $users = User::all();

         Post::all()->each(function ($post) use ($users) {
            $post->likedBy()->createMany(
                $users->random(rand(0, 10))->map(fn($user)
                => ['user_id' => $user->id])
            )->toArray();
        });
    }
}
