<?php

namespace App\Actions\Chat\CreateRoom;

use App\Models\User;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Auth;

class GetUsers
{
    public function __invoke(string $keyword, int ...$excludedUsers): Collection
    {
        return User::query()
            ->whereKeyNot(Auth::id())
            ->when(! empty($keyword))->whereLike('name', "%$keyword%")
            ->when(! empty($excludedUsers))->whereNotIn('id', $excludedUsers)
            ->orderBy('name')
            ->get();
    }
}
