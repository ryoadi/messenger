<?php

namespace App;

enum ChatRoomType: string
{
    case Direct = 'direct';
    case Group = 'group';
}
