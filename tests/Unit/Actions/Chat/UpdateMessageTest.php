<?php

declare(strict_types=1);

use App\Actions\Chat\UpdateMessage;
use App\Models\{ChatMessage, ChatRoom, User};

use function Pest\Laravel\actingAs;

it('updates content when changed and authorized', function () {
    $user = User::factory()->create();
    actingAs($user);

    $room = ChatRoom::factory()->create();
    /** @var ChatMessage $message */
    $message = ChatMessage::factory()->for($room, 'chatRoom')->for($user)->create([
        'content' => 'Old',
    ]);

    $updated = app(UpdateMessage::class)($message, '  New  ');

    expect($updated->content)->toBe('New');
});

it('does nothing when unchanged but still succeeds', function () {
    $user = User::factory()->create();
    actingAs($user);

    $room = ChatRoom::factory()->create();
    /** @var ChatMessage $message */
    $message = ChatMessage::factory()->for($room, 'chatRoom')->for($user)->create([
        'content' => 'Same',
    ]);

    $updated = app(UpdateMessage::class)($message, 'Same');

    expect($updated->content)->toBe('Same');
});

it('forbids non-owner', function () {
    $owner = User::factory()->create();
    $other = User::factory()->create();
    actingAs($other);

    $room = ChatRoom::factory()->create();
    /** @var ChatMessage $message */
    $message = ChatMessage::factory()->for($room, 'chatRoom')->for($owner)->create([
        'content' => 'Content',
    ]);

    app(UpdateMessage::class)($message, 'Hack');
})->throws(\Illuminate\Auth\Access\AuthorizationException::class);
