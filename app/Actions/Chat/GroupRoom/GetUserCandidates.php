<?php

namespace App\Actions\Chat\GroupRoom;

use App\Models\User;
use App\Models\ChatRoom;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Auth;

class GetUserCandidates
{
    /**
     * Return users who are not members of the given chat room.
     *
     * @param  ChatRoom $room
     * @param  string   $keyword
     * @param  int      ...$excludedUsers
     * @return Collection
     */
    public function __invoke(ChatRoom $room, string $keyword = '', int ...$excludedUsers): Collection
    {
        return User::query()
            ->whereKeyNot(Auth::id())
            ->when($keyword !== '', fn ($q) => $q->whereLike('name', "%{$keyword}%"))
            ->whereNotExists(function ($query) use ($room) {
                $query->selectRaw('1')
                    ->from('chat_room_user')
                    ->whereColumn('chat_room_user.user_id', 'users.id')
                    ->where('chat_room_user.chat_room_id', $room->id);
            })
            ->when(! empty($excludedUsers), fn ($q) => $q->whereNotIn('id', $excludedUsers))
            ->orderBy('name')
            ->get();
    }
}
