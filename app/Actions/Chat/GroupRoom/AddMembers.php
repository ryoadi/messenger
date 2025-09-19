<?php

namespace App\Actions\Chat\GroupRoom;

use App\Models\ChatRoom;

class AddMembers
{
    public function __invoke(ChatRoom $room, int ...$userIds): ChatRoom
    {
        $room->users()->syncWithoutDetaching($userIds);

        return $room->fresh();
    }
}
