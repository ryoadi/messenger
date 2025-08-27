<?php

declare(strict_types=1);

namespace App\Actions\Chat;

use App\Models\ChatRoom;
use App\Models\Enums\ChatRoomType;
use App\Models\Enums\ChatRoomUserRole;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

final class CreateRoom
{
    /**
     * Create a chat room or return an existing direct room when applicable.
     *
     * Rules:
     * - Selected users must not include the current user.
     * - If selecting exactly 1 user -> Direct room, title must be empty; if it exists, return it.
     * - If selecting 2+ users -> Group room, title is required.
     *
     * @param  int  $currentUserId  The authenticated user's ID
     * @param  array<int,int>  $selectedUserIds  Array of user IDs to add (excluding current user)
     * @param  string|null  $title  The room title for group rooms
     */
    public function __invoke(int $currentUserId, array $selectedUserIds, ?string $title = null): ChatRoom
    {
        // Normalize inputs
        $selectedUserIds = array_values(array_unique(array_map('intval', $selectedUserIds)));
        $selectedUserIds = array_values(array_filter($selectedUserIds, fn (int $id) => $id !== $currentUserId));

        if (count($selectedUserIds) < 1) {
            throw ValidationException::withMessages([
                'users' => 'Please select at least one user.',
            ]);
        }

        if (count($selectedUserIds) === 1) {
            // Direct room
            $otherUserId = $selectedUserIds[0];
            $title = Str::of((string) $title)->trim()->toString();
            if ($title !== '') {
                throw ValidationException::withMessages([
                    'name' => 'Title must be empty for a direct room.',
                ]);
            }

            // Try to find an existing direct room that has exactly these 2 users
            $existing = ChatRoom::query()
                ->where('type', ChatRoomType::Direct)
                ->whereHas('users', fn (Builder $q) => $q->whereKey($currentUserId))
                ->whereHas('users', fn (Builder $q) => $q->whereKey($otherUserId))
                ->withCount('users')
                ->where('users_count', 2)
                ->first();

            if ($existing) {
                return $existing;
            }

            $room = new ChatRoom();
            $room->type = ChatRoomType::Direct;
            $room->name = null;
            $room->save();

            // Attach members: current user as owner, other as member
            $room->users()->attach($currentUserId, ['role' => ChatRoomUserRole::Owner->value]);
            $room->users()->attach($otherUserId, ['role' => ChatRoomUserRole::Member->value]);

            return $room->refresh();
        }

        // Group room
        $title = Str::of((string) $title)->trim()->toString();
        if ($title === '') {
            throw ValidationException::withMessages([
                'name' => 'Group chat room title is required.',
            ]);
        }

        $room = new ChatRoom();
        $room->type = ChatRoomType::Group;
        $room->name = $title;
        $room->save();

        // Attach current user as owner
        $room->users()->attach($currentUserId, ['role' => ChatRoomUserRole::Owner->value]);

        // Attach others as members
        $attach = [];
        foreach ($selectedUserIds as $userId) {
            $attach[$userId] = ['role' => ChatRoomUserRole::Member->value];
        }
        if (! empty($attach)) {
            $room->users()->attach($attach);
        }

        return $room->refresh();
    }
}
