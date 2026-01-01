<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Post;
use App\Models\Category;
use App\Models\Tag;

class PostSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $categories=Category::all();
        $tags = Tag::all();

        Post::factory()->count(100)->create()->each(function ($post) use ($categories, $tags) {
            // Attach a random category to the post
            $post->categories()->attach($categories->random(rand(1,3)));

            // Attach random tags to the post
            $post->tags()->attach($tags->random(rand(1, 5)));
        });
    }
}
