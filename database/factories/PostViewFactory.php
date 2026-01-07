<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\PostView>
 */
class PostViewFactory extends Factory
{
    protected $model = \App\Models\PostView::class;

    public function definition(): array
    {
        return [
            'post_id' => \App\Models\Post::factory(),
            'visitor_ip' => fake()->ipv4(),
        ];
    }
}
