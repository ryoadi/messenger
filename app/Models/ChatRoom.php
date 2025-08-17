<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

use App\ChatRoomType;
use App\Models\User;
use App\Models\ChatMessage;

class ChatRoom extends Model
{
    /** @use HasFactory<\Database\Factories\ChatRoomFactory> */
    use HasFactory;

    protected $casts = [
        'type' => ChatRoomType::class,
    ];

    public function users()
    {
        return $this->belongsToMany(User::class)
            ->withPivot(['role'])
            ->withTimestamps();
    }

    public function messages()
    {
        return $this->hasMany(ChatMessage::class);
    }
}
