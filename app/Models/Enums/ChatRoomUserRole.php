<?php

namespace App\Models\Enums;

enum ChatRoomUserRole: string
{
    case Owner = 'owner';
    case Member = 'member';
}
