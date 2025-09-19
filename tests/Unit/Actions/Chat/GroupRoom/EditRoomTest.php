<?php

declare(strict_types=1);

use App\Models\ChatRoom;
use App\Actions\Chat\GroupRoom\EditRoom;

it('edits the room name', function () {
    $room = ChatRoom::factory()->create(['name' => 'Old Name']);
    (new EditRoom)($room, 'New Name');
    expect($room->fresh()->name)->toBe('New Name');
});
