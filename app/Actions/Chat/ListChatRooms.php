<?php

declare(strict_types=1);

namespace App\Actions\Chat;

use App\ChatRoomType;
use App\Models\ChatRoom;
use Illuminate\Contracts\Pagination\Paginator;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;

final class ListChatRooms
{
    /**
     * Execute the listing.
     *
     * @return Paginator|Collection<ChatRoom>
     */
    public function handle(ListChatRoomsQuery $query): Paginator|Collection
    {
        $builder = $this->asQuery($query)
            ->when(! empty($query->with), fn (Builder $q) => $q->with($query->with))
            ->orderByDesc('updated_at');

        return $query->paginate
            ? $builder->simplePaginate($query->perPage)
            : $builder->get();
    }

    /** Return the core query so callers can extend it if needed. */
    public function asQuery(ListChatRoomsQuery $q): Builder
    {
        $term = $q->trimmedSearch();

        return ChatRoom::query()
            // Membership: rooms where the user is a member
            ->whereHas('users', fn (Builder $u) => $u->whereKey($q->userId))

            // Optional type filter: direct/group; null means all
            ->when($q->type instanceof ChatRoomType, fn (Builder $b) => $b->where('type', $q->type))

            // Search rules:
            // - Group rooms by title (name LIKE %term%)
            // - Direct rooms by the other participant's name
            ->when($term !== '', function (Builder $b) use ($term, $q) {
                $b->where(function (Builder $searchQ) use ($term, $q) {
                    $searchQ
                        // Group title search
                        ->where(function (Builder $groupQ) use ($term) {
                            $groupQ->where('type', ChatRoomType::Group)
                                   ->where('name', 'like', "%{$term}%");
                        })
                        // Direct search by other user name
                        ->orWhere(function (Builder $directQ) use ($term, $q) {
                            $directQ->where('type', ChatRoomType::Direct)
                                    ->whereHas('users', function (Builder $userQ) use ($term, $q) {
                                        $userQ->where('users.id', '!=', $q->userId)
                                              ->where('users.name', 'like', "%{$term}%");
                                    });
                        });
                });
            })

            // Keep explicit table select
            ->select('chat_rooms.*');
    }
}
