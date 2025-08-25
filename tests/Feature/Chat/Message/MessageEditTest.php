<?php

declare(strict_types=1);

use App\Models\ChatMessage;
use App\Models\ChatRoom;
use App\Models\User;
use Livewire\Volt\Volt;

use function Pest\Laravel\actingAs;

it('allows the owner to edit a message and persists the change', function () {
    $user = User::factory()->create();
    actingAs($user);

    $room = ChatRoom::factory()->create();

    /** @var ChatMessage $message */
    $message = ChatMessage::factory()->for($room, 'chatRoom')->for($user)->create([
        'content' => 'Old content',
    ]);

    $new = '  New content  ';

    Volt::test('chat.message', [
        'message' => $message,
        'group' => false,
    ])->call('edit', content: $new)
        ->assertSuccessful();

    // The content should be trimmed and persisted
    $message->refresh();
    expect($message->content)->toBe('New content');
});

it('forbids editing a message not owned by the user', function () {
    $owner = User::factory()->create();
    $other = User::factory()->create();
    actingAs($other);

    $room = ChatRoom::factory()->create();

    /** @var ChatMessage $message */
    $message = ChatMessage::factory()->for($room, 'chatRoom')->for($owner)->create([
        'content' => 'Owner content',
    ]);

    Volt::test('chat.message', [
        'message' => $message,
        'group' => false,
    ])->call('edit', content: 'Hacked')
        ->assertForbidden();

    // Ensure content unchanged
    $message->refresh();
    expect($message->content)->toBe('Owner content');
});
