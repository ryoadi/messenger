<?php

declare(strict_types=1);

namespace App\Actions\Chat;

use App\Models\ChatMessage;
use App\Models\ChatRoom;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Str;

final class CreateMessage
{
    public function __invoke(ChatRoom $room, string $rawContent): ChatMessage
    {
        Gate::authorize('addMessage', $room);

        $content = Str::trim($rawContent);
        validator(
            ['content' => $content],
            ['content' => ['required', 'string']]
        )->validate();

        $newMessage = ChatMessage::create([
            'chat_room_id' => $room->getKey(),
            'user_id' => (int) Auth::id(),
            'content' => $content,
        ]);
        $newMessage->save();

        return $newMessage;
    }
}
