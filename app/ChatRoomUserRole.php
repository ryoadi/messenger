<?php

namespace App;

enum ChatRoomUserRole: string
{
    case Owner = 'owner';
    case Member = 'member';
}
