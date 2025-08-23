<?php

declare(strict_types=1);

namespace App\Policies;

use App\Models\ChatRoom;
use App\Models\User;

class ChatRoomPolicy
{
    /**
     * Determine whether the user can add a message to the given chat room.
     */
    public function addMessage(User $user, ChatRoom $chatRoom): bool
    {
        // User must belong to the chat room to add a message
        return $chatRoom->users()->whereKey($user->getKey())->exists();
    }
}
