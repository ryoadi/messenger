<?php

declare(strict_types=1);

namespace App\Actions\Chat\CreateRoom;

use App\Models\ChatRoom;
use App\Models\Enums\ChatRoomType;
use App\Models\Enums\ChatRoomUserRole;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

final class CreateDirectRoom
{
    /**
     * Create or return an existing direct room between two users.
     */
    public function __invoke(int $currentUserId, int $otherUserId): ChatRoom
    {
        // Prevents selecting self
        Validator::validate(
            [
                'current_user_id' => $currentUserId,
                'other_user_id' => $otherUserId,
            ],
            [
                'other_user_id' => ['different:current_user_id'],
            ],
            [
                'other_user_id.different' => 'Cannot create a direct room with yourself.',
            ]
        );

        // Try to find an existing direct room that has exactly these 2 users.
        // Just return it instead of creating new room, if we find one.
        $existingRoom = ChatRoom::query()
            ->where('type', ChatRoomType::Direct)
            ->whereHas('users', fn (Builder $userQuery) => $userQuery->whereKey($currentUserId))
            ->whereHas('users', fn (Builder $userQuery) => $userQuery->whereKey($otherUserId))
            ->where('users_count', 2)
            ->withCount('users')
            ->first();
        if ($existingRoom) {
            return $existingRoom;
        }

        $room = new ChatRoom;
        $room->type = ChatRoomType::Direct;

        DB::transaction(function () use ($room, $currentUserId, $otherUserId): void {
            $room->save();
            $room->users()->attach($currentUserId, ['role' => ChatRoomUserRole::Member->value]);
            $room->users()->attach($otherUserId, ['role' => ChatRoomUserRole::Member->value]);
        });

        return $room->refresh();
    }
}
