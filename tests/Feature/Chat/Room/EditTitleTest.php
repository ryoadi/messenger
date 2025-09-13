<?php

declare(strict_types=1);

use App\Models\ChatRoom;
use App\Models\User;
use App\Models\Enums\ChatRoomUserRole;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Volt\Volt;

use function Pest\Laravel\actingAs;

it('non owner cannot update the room title via volt', function () {
    $owner = User::factory()->create();
    $other = User::factory()->create();

    $room = ChatRoom::factory()->group()->create();
    $room->users()->attach($owner->id, ['role' => ChatRoomUserRole::Owner->value]);
    $room->users()->attach($other->id, ['role' => ChatRoomUserRole::Member->value]);

    actingAs($other);

    Volt::test('chat.profile.group', ['room' => $room])
        ->set('title', 'New Title')
        ->call('updateTitle')
        ->assertForbidden();
});

it('validation prevents empty input', function () {
    $owner = User::factory()->create();

    $room = ChatRoom::factory()->group()->create();
    $room->users()->attach($owner->id, ['role' => ChatRoomUserRole::Owner->value]);

    actingAs($owner);

    Volt::test('chat.profile.group', ['room' => $room])
        ->set('title', '')
    ->call('updateTitle')
    ->assertHasErrors(['title'])
    ->assertSee('The title field is required');
});

it('owner can update the room title', function () {
    $owner = User::factory()->create();

    $room = ChatRoom::factory()->group()->create();
    $room->users()->attach($owner->id, ['role' => ChatRoomUserRole::Owner->value]);

    actingAs($owner);

    Volt::test('chat.profile.group', ['room' => $room])
        ->set('title', 'Updated Room Title')
        ->call('updateTitle')
        ->assertHasNoErrors();

    // component should render the updated title immediately
    Volt::test('chat.profile.group', ['room' => $room->fresh()])
        ->assertSee('Updated Room Title');

    expect($room->fresh()->name)->toBe('Updated Room Title');
});
