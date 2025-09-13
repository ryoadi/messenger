<?php

declare(strict_types=1);

use App\Models\ChatRoom;
use App\Models\User;
use App\Actions\Chat\GroupRoom\RemoveMembers;

it('removes members from a room', function () {
    $room = ChatRoom::factory()->create();
    $users = User::factory()->count(2)->create();
    $room->users()->attach($users->pluck('id')->all());

    (new RemoveMembers)($room, $users[0]->id);

    expect($room->users()->where('users.id', $users[0]->id)->exists())->toBeFalse();
    expect($room->users()->where('users.id', $users[1]->id)->exists())->toBeTrue();
});
