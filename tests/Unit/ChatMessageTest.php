<?php

use App\Models\ChatMessage;
use App\Models\ChatRoom;
use App\Models\User;

it('chat message factory creates valid message', function () {
    $message = ChatMessage::factory()->create();
    expect($message->content)->not->toBeEmpty()
        ->and($message->room)->toBeInstanceOf(ChatRoom::class)
        ->and($message->sender)->toBeInstanceOf(User::class);
});

it('chat message belongs to user and room', function () {
    $room = ChatRoom::factory()->create();
    $user = User::factory()->create();
    $message = ChatMessage::factory()->create([
        'chat_room_id' => $room->id,
        'user_id' => $user->id,
    ]);
    expect($message->room->id)->toBe($room->id)
        ->and($message->sender->id)->toBe($user->id);
});
