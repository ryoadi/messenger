<?php

declare(strict_types=1);

use App\Actions\Chat\CreateMessage;
use App\Models\{ChatMessage, ChatRoom, User};
use Illuminate\Validation\ValidationException;

use function Pest\Laravel\actingAs;

it('creates a message when valid and authorized', function () {
    $user = User::factory()->create();
    actingAs($user);

    $room = ChatRoom::factory()->create();
    $room->users()->attach($user->getKey(), ['role' => 'member']);

    $message = app(CreateMessage::class)($room, ' Hello ');

    expect($message)->toBeInstanceOf(ChatMessage::class)
        ->and($message->content)->toBe('Hello')
        ->and($message->user_id)->toBe($user->getKey())
        ->and($message->chat_room_id)->toBe($room->getKey());
});

it('rejects whitespace-only messages', function () {
    $user = User::factory()->create();
    actingAs($user);

    $room = ChatRoom::factory()->create();
    $room->users()->attach($user->getKey(), ['role' => 'member']);

    app(CreateMessage::class)($room, '   ');
})->throws(ValidationException::class);

it('forbids non-members', function () {
    $user = User::factory()->create();
    actingAs($user);

    $room = ChatRoom::factory()->create();

    app(CreateMessage::class)($room, 'Hi');
})->throws(\Illuminate\Auth\Access\AuthorizationException::class);
