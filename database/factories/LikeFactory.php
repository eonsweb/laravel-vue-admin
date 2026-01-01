<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Like>
 */
class LikeFactory extends Factory
{
    protected $model = \App\Models\Like::class;

    public function definition(): array
    {
        return [
            'post_id' => \App\Models\Post::factory(),
            'user_id' => \App\Models\User::factory(),
        ];
    }
}
