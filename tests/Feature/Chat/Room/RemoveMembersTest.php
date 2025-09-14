<?php

declare(strict_types=1);

use App\Models\ChatRoom;
use App\Models\User;
use App\Models\Enums\ChatRoomUserRole;
use Livewire\Volt\Volt;

use function Pest\Laravel\actingAs;

it('owner can remove a member from the room', function () {
    $owner = User::factory()->create();
    $member = User::factory()->create();

    $room = ChatRoom::factory()->group()->create();
    $room->users()->attach($owner->id, ['role' => ChatRoomUserRole::Owner->value]);
    $room->users()->attach($member->id, ['role' => ChatRoomUserRole::Member->value]);

    actingAs($owner);

    Volt::test('chat.profile.group', ['room' => $room])
        ->call('removeMembers', $member->id)
        ->assertHasNoErrors();

    // Member should no longer be attached
    expect($room->fresh()->users->contains($member->id))->toBeFalse();

    // Component render should not show the removed member
    Volt::test('chat.profile.group', ['room' => $room->fresh()])
        ->assertDontSee($member->name);
});

it('non owner cannot remove members', function () {
    $owner = User::factory()->create();
    $attacker = User::factory()->create();
    $member = User::factory()->create();

    $room = ChatRoom::factory()->group()->create();
    $room->users()->attach($owner->id, ['role' => ChatRoomUserRole::Owner->value]);
    $room->users()->attach($attacker->id, ['role' => ChatRoomUserRole::Member->value]);
    $room->users()->attach($member->id, ['role' => ChatRoomUserRole::Member->value]);

    actingAs($attacker);

    Volt::test('chat.profile.group', ['room' => $room])
        ->call('removeMembers', $member->id)
        ->assertForbidden();
});

it('attempting to remove an owner is ignored (owner remains)', function () {
    $owner = User::factory()->create();

    $room = ChatRoom::factory()->group()->create();
    $room->users()->attach($owner->id, ['role' => ChatRoomUserRole::Owner->value]);

    actingAs($owner);

    // Owner attempts to remove themselves (or another owner) — action should ignore owners
    Volt::test('chat.profile.group', ['room' => $room])
        ->call('removeMembers', $owner->id)
        ->assertHasNoErrors();

    // Owner should still be attached
    expect($room->fresh()->users->contains($owner->id))->toBeTrue();
});
