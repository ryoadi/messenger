<?php

use App\ChatRoomUserRole;
use App\Models\ChatRoom;
use App\Models\User;
use Illuminate\Validation\ValidationException;

it('only allows 2 unique users', function () {
    $room = ChatRoom::factory()->direct()->create();
    $users = User::factory()->count(3)->create();
    $room->users()->attach($users[0], ['role' => ChatRoomUserRole::Owner]);
    $room->users()->attach($users[1], ['role' => ChatRoomUserRole::Member]);
    $room->users()->attach($users[2], ['role' => ChatRoomUserRole::Member]);
    expect(fn () => $room->save())
        ->toThrow(ValidationException::class, 'Direct chat room can only have 2 users.');
});

it('allows more than 2 users', function () {
    $room = ChatRoom::factory()->group()->create();
    $users = User::factory()->count(5)->create();
    foreach ($users as $i => $user) {
        $room->users()->attach($user, ['role' => $i === 0 ? ChatRoomUserRole::Owner : ChatRoomUserRole::Member]);
    }
    expect($room->users()->count())->toBe(5);
});

it('requires a title', function () {
    $room = ChatRoom::factory()->group()->make(['name' => null]);
    expect(fn () => $room->save())
        ->toThrow(ValidationException::class, 'Group chat room title is required.');
});

it('returns correct roles', function () {
    $room = ChatRoom::factory()->group()->create();
    $owner = User::factory()->create();
    $member = User::factory()->create();
    $room->users()->attach($owner, ['role' => ChatRoomUserRole::Owner]);
    $room->users()->attach($member, ['role' => ChatRoomUserRole::Member]);
    $roles = $room->users->pluck('pivot.role', 'id');
    expect($roles[$owner->id])->toBe(ChatRoomUserRole::Owner->value)
        ->and($roles[$member->id])->toBe(ChatRoomUserRole::Member->value);
});

it('has many messages', function () {
    $room = ChatRoom::factory()->create();
    $user = User::factory()->create();
    $room->messages()->create([
        'user_id' => $user->id,
        'content' => 'Hello',
    ]);
    expect($room->messages)->toHaveCount(1);
});
