<?php

declare(strict_types=1);

namespace App\Actions\Chat;

use App\Models\ChatMessage;
use App\Models\ChatRoom;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use Illuminate\Validation\ValidationException;

final class CreateMessage
{
    public function __invoke(ChatRoom $room, string $rawContent): ChatMessage
    {
        $validated = validator(
            ['content' => $rawContent],
            ['content' => ['required', 'string']]
        )->validate();

        $content = trim((string) $validated['content']);
        if ($content === '') {
            throw ValidationException::withMessages([
                'content' => [__('The message cannot be empty.')],
            ]);
        }

        Gate::authorize('addMessage', $room);

        return ChatMessage::query()->create([
            'chat_room_id' => $room->getKey(),
            'user_id' => (int) Auth::id(),
            'content' => $content,
        ]);
    }
}
