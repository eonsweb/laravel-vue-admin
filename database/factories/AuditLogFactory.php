<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\User;
use App\Models\AuditLog;
/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\AuditLog>
 */
class AuditLogFactory extends Factory
{
    protected $model = \App\Models\AuditLog::class;

    public function definition(): array
    {
         return [
            'user_id' => $this->faker->boolean(80)   // 80% chance logs belong to a user
                ? User::factory()
                : null,

            'action' => $this->faker->randomElement([
                'login', 'logout', 'create', 'update', 'delete', 'publish', 'unpublish'
            ]),

            'description' => $this->faker->optional()->sentence(),

            'ip_address' => $this->faker->optional()->ipv4(),

            'target_id' => $this->faker->optional()->randomDigitNotNull(),
            'target_type' => $this->faker->optional()->randomElement([
                'post', 'user', 'comment', 'category', 'tag'
            ]),

            'details' => $this->faker->optional()->randomElement([
                ['old' => 'draft', 'new' => 'published'],
                ['field' => 'title', 'old' => 'Test', 'new' => 'New Title'],
                ['meta_key' => 'seo_title'],
                ['status' => 'success'],
            ]),
        ];
    }
}
