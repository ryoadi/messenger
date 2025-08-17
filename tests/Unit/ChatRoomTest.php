<?php

use App\Models\ChatRoom;
use App\Models\User;
use App\ChatRoomType;
use Illuminate\Database\QueryException;

it('chat room factory creates valid direct and group rooms', function () {
    $direct = ChatRoom::factory()->direct()->create();
    $group = ChatRoom::factory()->group()->create();
    expect($direct->type)->toBe(ChatRoomType::Direct)
        ->and($group->type)->toBe(ChatRoomType::Group);
});

it('direct chat room only allows 2 unique users', function () {
    $room = ChatRoom::factory()->direct()->create();
    $users = User::factory()->count(3)->create();
    $room->users()->attach($users[0], ['role' => \App\ChatRoomUserRole::Owner]);
    $room->users()->attach($users[1], ['role' => \App\ChatRoomUserRole::Member]);
    $room->users()->attach($users[2], ['role' => \App\ChatRoomUserRole::Member]);
    expect(fn() => $room->save())
        ->toThrow(\Illuminate\Validation\ValidationException::class, 'Direct chat room can only have 2 users.');
});

it('group chat room allows more than 2 users', function () {
    $room = ChatRoom::factory()->group()->create();
    $users = User::factory()->count(5)->create();
    foreach ($users as $i => $user) {
        $room->users()->attach($user, ['role' => $i === 0 ? \App\ChatRoomUserRole::Owner : \App\ChatRoomUserRole::Member]);
    }
    expect($room->users()->count())->toBe(5);
});

it('chat room users relationship returns correct roles', function () {
    $room = ChatRoom::factory()->group()->create();
    $owner = User::factory()->create();
    $member = User::factory()->create();
    $room->users()->attach($owner, ['role' => \App\ChatRoomUserRole::Owner]);
    $room->users()->attach($member, ['role' => \App\ChatRoomUserRole::Member]);
    $roles = $room->users->pluck('pivot.role', 'id');
    expect($roles[$owner->id])->toBe(\App\ChatRoomUserRole::Owner->value)
        ->and($roles[$member->id])->toBe(\App\ChatRoomUserRole::Member->value);
});

it('chat room has many messages', function () {
    $room = ChatRoom::factory()->create();
    $user = User::factory()->create();
    $room->messages()->create([
        'user_id' => $user->id,
        'content' => 'Hello',
    ]);
    expect($room->messages)->toHaveCount(1);
});
