<?php

declare(strict_types=1);

namespace App\Policies;

use App\Models\ChatMessage;
use App\Models\User;

class ChatMessagePolicy
{
    /**
     * Determine whether the user can manage the chat message.
     */
    public function manage(User $user, ChatMessage $message): bool
    {
        return $message->user_id === $user->getKey();
    }
}
