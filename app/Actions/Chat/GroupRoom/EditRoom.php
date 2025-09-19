<?php

namespace App\Actions\Chat\GroupRoom;

use App\Models\ChatRoom;


class EditRoom
{
    public function __invoke(ChatRoom $room, string $newName): ChatRoom
    {
        $room->update(['name' => $newName]);

        return $room->fresh();
    }
}