<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

use App\Models\User;
use App\Models\ChatRoom;

class ChatMessage extends Model
{
    /** @use HasFactory<\Database\Factories\ChatMessageFactory> */
    use HasFactory;

    protected $fillable = [
        'user_id',
        'content',
    ];

    public function room()
    {
        return $this->belongsTo(ChatRoom::class, 'chat_room_id');
    }

    public function sender()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
