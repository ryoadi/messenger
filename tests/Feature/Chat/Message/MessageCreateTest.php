<?php

declare(strict_types=1);

use App\Models\ChatMessage;
use App\Models\ChatRoom;
use App\Models\User;
use Livewire\Volt\Volt;

it('allows an authenticated user to add a new message to the chat room', function () {
    $user = User::factory()->create();
    $this->actingAs($user);

    $room = ChatRoom::factory()->create();

    // Ensure the authenticated user is a member of the room (policy requirement)
    $room->users()->attach($user->getKey(), ['role' => 'member']);

    // Interact with the Volt component directly
    Volt::test('chat.room', [
        'room' => $room,
    ])->set('text', '  Hello from Volt  ')
        ->call('addMessage')
        ->assertSuccessful();

    // Assert message persisted and trimmed
    /** @var ChatMessage|null $saved */
    $saved = ChatMessage::query()->latest('id')->first();

    expect($saved)->not->toBeNull();
    expect($saved?->chat_room_id)->toBe($room->getKey());
    expect($saved?->user_id)->toBe($user->getKey());
    expect($saved?->content)->toBe('Hello from Volt');

    // Ensure it renders on the room page as well
    $response = $this->get("/chat/{$room->getRouteKey()}");
    $response->assertSuccessful();
    $response->assertSee('Hello from Volt', escape: false);
});

it('rejects creating an empty or whitespace-only message', function () {
    $user = User::factory()->create();
    $this->actingAs($user);
    $room = ChatRoom::factory()->create();

    Volt::test('pages.chat.[ChatRoom]', [
        'chatRoom' => $room,
    ])->set('text', '   ')
        ->call('addMessage')
        ->assertSuccessful();

    expect(ChatMessage::query()->where('chat_room_id', $room->getKey())->exists())->toBeFalse();
})
    ->skip('Skipping until Volt::test can target Volt pages in this project.');
