<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class PostFactory extends Factory
{
    protected $model = \App\Models\Post::class;


    public function definition(): array
    {
        $title = fake()->sentence();

        return [
            'user_id' => \App\Models\User::factory(),
            'title' => $title,
            'content' => fake()->paragraphs(3, true),
            'slug' => Str::slug($title) . '-' . Str::random(5),
            'excerpt' => fake()->text(200),
            'featured_image' => null,
            'status' => fake()->randomElement(['draft', 'review', 'published', 'archived']),
            'is_featured' => fake()->boolean(20),
            'allow_comments' => true,
            'view_count' => fake()->numberBetween(0, 1000),
            'published_at' => now(),
        ];
    }
}
