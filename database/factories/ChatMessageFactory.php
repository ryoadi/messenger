<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\ChatMessage>
 */
class ChatMessageFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'chat_room_id' => fn() => \App\Models\ChatRoom::factory(),
            'user_id' => fn() => \App\Models\User::factory(),
            'content' => $this->faker->text(32),
            'read_at' => null,
        ];
    }
}
