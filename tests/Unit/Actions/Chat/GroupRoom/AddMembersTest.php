<?php

declare(strict_types=1);

use App\Models\ChatRoom;
use App\Models\User;
use App\Actions\Chat\GroupRoom\AddMembers;

it('adds members to a room', function () {
    $room = ChatRoom::factory()->create();
    $users = User::factory()->count(2)->create();

    (new AddMembers)($room, ...$users->pluck('id')->all());

    foreach ($users as $user) {
        expect($room->users()->where('users.id', $user->id)->exists())->toBeTrue();
    }
});
