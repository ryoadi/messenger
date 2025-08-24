<?php

declare(strict_types=1);

use App\Actions\Chat\DeleteMessage;
use App\Models\{ChatMessage, ChatRoom, User};

use function Pest\Laravel\actingAs;

it('deletes when authorized', function () {
    $user = User::factory()->create();
    actingAs($user);

    $room = ChatRoom::factory()->create();
    /** @var ChatMessage $message */
    $message = ChatMessage::factory()->for($room, 'chatRoom')->for($user)->create([
        'content' => 'To delete',
    ]);

    app(DeleteMessage::class)($message);

    expect(ChatMessage::query()->whereKey($message->getKey())->exists())->toBeFalse();
});

it('forbids non-owner', function () {
    $owner = User::factory()->create();
    $other = User::factory()->create();
    actingAs($other);

    $room = ChatRoom::factory()->create();
    /** @var ChatMessage $message */
    $message = ChatMessage::factory()->for($room, 'chatRoom')->for($owner)->create([
        'content' => 'Keep',
    ]);

    app(DeleteMessage::class)($message);
})->throws(\Illuminate\Auth\Access\AuthorizationException::class);
