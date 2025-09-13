<?php

namespace App\Actions\Chat\GroupRoom;

use App\Models\ChatRoom;

class RemoveMembers
{
    public function __invoke(ChatRoom $room, int ...$userIds): ChatRoom
    {
        $room->users()->detach($userIds);

        return $room->fresh();
    }
}
