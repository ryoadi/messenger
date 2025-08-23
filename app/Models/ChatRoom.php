<?php

namespace App\Models;

use App\ChatRoomType;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Validation\ValidationException;

class ChatRoom extends Model
{
    /** @use HasFactory<\Database\Factories\ChatRoomFactory> */
    use HasFactory;

    protected $casts = [
        'type' => ChatRoomType::class,
    ];

    protected static function booted()
    {
        static::saving(function (self $chatRoom) {
            if ($chatRoom->type === ChatRoomType::Direct) {
                // If the chat room already exists, check user count
                if ($chatRoom->exists) {
                    $userCount = $chatRoom->users()->distinct()->count();
                    if ($userCount > 2) {
                        throw ValidationException::withMessages([
                            'users' => 'Direct chat room can only have 2 users.',
                        ]);
                    }
                }
            }
            if ($chatRoom->type === ChatRoomType::Group) {
                if (empty($chatRoom->name)) {
                    throw ValidationException::withMessages([
                        'name' => 'Group chat room title is required.',
                    ]);
                }
            }
        });
    }

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
