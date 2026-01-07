<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Media>
 */
class MediaFactory extends Factory
{
    protected $model = \App\Models\Media::class;

    public function definition(): array
    {
        return [
            'user_id' => \App\Models\User::factory(),
            'file_name'=> fake()->word() . '.' . fake()->fileExtension(),
            'file_path'=> 'uploads/' . fake()->word() . '.' . fake()->fileExtension(),
            'file_type'=> fake()->mimeType(),
            'file_size'=> fake()->numberBetween(1000, 5000000),
            'disk'=> 'public',
            'alt_text'=> fake()->sentence(),
        ];
    }
}
