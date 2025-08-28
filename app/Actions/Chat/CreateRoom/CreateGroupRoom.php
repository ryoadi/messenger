<?php

declare(strict_types=1);

namespace App\Actions\Chat\CreateRoom;

use App\Actions\Chat\DTO\CreateGroupRoomInput;
use App\Models\ChatRoom;
use App\Models\Enums\ChatRoomType;
use App\Models\Enums\ChatRoomUserRole;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

final class CreateGroupRoom
{
    /**
     * Create a new group chat room with the given users and title.
     */
    public function __invoke(CreateGroupRoomInput $data): ChatRoom
    {
        // Validate using Laravel Validator
        Validator::validate(
            [
                'users' => $data->selectedUserIds->all(),
                'name' => $data->name,
            ],
            [
                'users' => ['array', 'min:2'],
                'name' => ['required', 'string'],
            ],
            [
                'users.min' => 'Please select at least two users for a group room.',
                'name.required' => 'Group chat room title is required.',
            ]
        );

        $room = new ChatRoom;
        $room->type = ChatRoomType::Group;
        $room->name = $data->name;

        DB::transaction(function () use ($room, $data) {
            $room->save();

            // Attach current user as owner,
            // Attach others as members
            $room->users()->attach($data->currentUserId, ['role' => ChatRoomUserRole::Owner->value]);
            $data->selectedUserIds->each(fn (int $userId) => $room->users()->attach(
                $userId, ['role' => ChatRoomUserRole::Member->value],
            ));
        });

        return $room->refresh();
    }
}
