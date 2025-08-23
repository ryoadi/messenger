<?php

declare(strict_types=1);

use App\Models\ChatMessage;
use App\Models\ChatRoom;
use App\Models\User;

it('renders chat message content on the chat room page', function () {
    $user = User::factory()->create();
    $this->actingAs($user);

    $room = ChatRoom::factory()->create();

    /** @var ChatMessage $message */
    $message = ChatMessage::factory()->for($room, 'chatRoom')->for($user)->create([
        'content' => 'Hello from the test message!',
        'created_at' => now()->setTime(10, 0),
    ]);

    $response = $this->get("/chat/{$room->getRouteKey()}");

    $response->assertSuccessful();
    $response->assertSee('Hello from the test message!', escape: false);
});
