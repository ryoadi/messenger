<?php

use App\Models\ChatRoom;
use App\Models\User;
use Illuminate\Support\Facades\Broadcast;

Broadcast::channel('chat.room.{room}', fn (User $user, ChatRoom $room) => $user->can('view', $room));
