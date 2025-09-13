<?php

declare(strict_types=1);

namespace App\Models\Policies;

use App\Models\ChatRoom;
use App\Models\User;
use App\Models\Enums\ChatRoomUserRole;

class ChatRoomPolicy
{
    /**
     * Determine whether the user can view the given chat room.
     */
    public function view(User $user, ChatRoom $chatRoom): bool
    {
        return $chatRoom->users()->whereKey($user->getKey())->exists();
    }

    /**
     * Determine whether the user can add a message to the given chat room.
     */
    public function addMessage(User $user, ChatRoom $chatRoom): bool
    {
        // User must belong to the chat room to add a message
        return $chatRoom->users()->whereKey($user->getKey())->exists();
    }

    /**
     * Determine whether the user can manage the given chat room.
     */
    public function manage(User $user, ChatRoom $chatRoom): bool
    {
        // Return true if the user's pivot role for the room is owner
        return $chatRoom->users()
            ->whereKey($user->getKey())
            ->wherePivot('role', ChatRoomUserRole::Owner->value)
            ->exists();
    }
}
