<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class PostMetaFactory extends Factory
{
    protected $model = \App\Models\PostMeta::class;

    public function definition(): array
    {
        return [
            'post_id' => \App\Models\Post::factory(),
            'meta_key' => fake()->word(),
            'meta_value' => fake()->sentence(),
        ];
    }
}
