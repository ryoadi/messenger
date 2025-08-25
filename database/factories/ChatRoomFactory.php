<?php

namespace Database\Factories;

use App\Models\Enums\ChatRoomType;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\ChatRoom>
 */
class ChatRoomFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => $this->faker->text(16),
            'type' => $this->faker->randomElement(['direct', 'group']),
        ];
    }

    public function direct(): static
    {
        return $this->state(fn () => [
            'type' => ChatRoomType::Direct,
        ]);
    }

    public function group(): static
    {
        return $this->state(fn () => [
            'type' => ChatRoomType::Group,
        ]);
    }
}
