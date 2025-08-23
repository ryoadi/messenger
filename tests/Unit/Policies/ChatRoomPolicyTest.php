<?php

declare(strict_types=1);

use App\Models\ChatRoom;
use App\Models\User;
use Illuminate\Support\Facades\Gate;

it('allows adding a message when the user belongs to the chat room', function () {
    $user = User::factory()->create();
    $room = ChatRoom::factory()->create();
    // attach user to room with required pivot fields
    $room->users()->attach($user->getKey(), ['role' => 'member']);

    expect(Gate::forUser($user)->allows('addMessage', $room))->toBeTrue();
});

it('forbids adding a message when the user does not belong to the chat room', function () {
    $user = User::factory()->create();
    $room = ChatRoom::factory()->create();

    expect(Gate::forUser($user)->denies('addMessage', $room))->toBeTrue();
});
