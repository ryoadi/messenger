<?php

namespace App\Models\Enums;

enum ChatRoomType: string
{
    case Direct = 'direct';
    case Group = 'group';
}
