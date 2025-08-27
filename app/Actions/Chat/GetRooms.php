<?php

declare(strict_types=1);

namespace App\Actions\Chat;

use App\Actions\Chat\DTO\ListRoomsFilter;
use App\Models\Enums\ChatRoomType;
use App\Models\ChatRoom;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;

final class GetRooms
{
    public function __invoke(ListRoomsFilter $filters): Collection
    {
        return ChatRoom::query()
            ->orderByDesc('updated_at')

            // Membership: rooms where the user is a member
            ->whereHas('users', fn (Builder $userQuery) => $userQuery->whereKey($filters->userId))

            // Optional type filter: direct/group; null means all
            ->when(!empty($filters->type))->where('type', $filters->type)

            // Search rules:
            // - Group rooms by title (name LIKE %term%)
            // - Direct rooms by the other participant's name
            ->when($filters->keyword)->where(
                fn (Builder $query) => $query
                    // Group title search
                    ->where(fn (Builder $query) => $query
                        ->where('type', ChatRoomType::Group)
                        ->whereLike('name', "%{$filters->keyword}%")
                    )
                    // Direct search by other user name
                    ->orWhere(fn (Builder $query) => $query
                        ->where('type', ChatRoomType::Direct)
                        ->whereHas('users',
                            fn (Builder $userQuery) => $userQuery
                                ->whereNot('users.id', $filters->userId)
                                ->whereLike('users.name', "%{$filters->keyword}%")
                        )
                    )
            )

            // No Pagination.
            ->get();
    }
}
