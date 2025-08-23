<?php

declare(strict_types=1);

use App\Models\ChatMessage;
use App\Models\ChatRoom;
use App\Models\User;
use Livewire\Volt\Volt;

it('allows the owner to delete their message', function () {
    $user = User::factory()->create();
    $this->actingAs($user);

    $room = ChatRoom::factory()->create();

    /** @var ChatMessage $message */
    $message = ChatMessage::factory()->for($room, 'chatRoom')->for($user)->create([
        'content' => 'Delete me',
    ]);

    Volt::test('chat.message', [
        'message' => $message,
        'group' => false,
    ])->call('delete')->assertSuccessful();

    expect(ChatMessage::query()->whereKey($message->getKey())->exists())->toBeFalse();
});

it('forbids deleting a message not owned by the user', function () {
    $owner = User::factory()->create();
    $other = User::factory()->create();
    $this->actingAs($other);

    $room = ChatRoom::factory()->create();

    /** @var ChatMessage $message */
    $message = ChatMessage::factory()->for($room, 'chatRoom')->for($owner)->create([
        'content' => 'Should not delete',
    ]);

    Volt::test('chat.message', [
        'message' => $message,
        'group' => false,
    ])->call('delete')->assertForbidden();

    expect(ChatMessage::query()->whereKey($message->getKey())->exists())->toBeTrue();
});
