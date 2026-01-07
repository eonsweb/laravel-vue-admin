<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\RelatedPost>
 */
class RelatedPostFactory extends Factory
{
    protected $model = \App\Models\RelatedPost::class;

    public function definition(): array
    {
        return [
            'post_id' => \App\Models\Post::factory(),
            'related_post_id' => \App\Models\Post::factory(),
        ];
    }
}
