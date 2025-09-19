<?php

namespace App\Actions\Chat\GroupRoom;

use App\Models\ChatRoom;
use App\Models\Enums\ChatRoomUserRole;

class RemoveMembers
{
    public function __invoke(ChatRoom $room, int ...$userIds): ChatRoom
    {
        if (empty($userIds)) {
            return $room->fresh();
        }

        // Only detach users who are attached to this room and are NOT owners
        $removableIds = $room->users()
            ->whereIn('users.id', $userIds)
            ->wherePivot('role', '!=', ChatRoomUserRole::Owner->value)
            ->pluck('users.id')
            ->all();

        if (! empty($removableIds)) {
            $room->users()->detach($removableIds);
        }

        return $room->fresh();
    }
}
