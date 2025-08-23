<?php

use App\Models\ChatMessage;
use Illuminate\Database\QueryException;

it('requires a user', function () {
    expect(function () {
        ChatMessage::factory()->create([
            'user_id' => null,
        ]);
    })->toThrow(QueryException::class);
});

it('requires a room', function () {
    expect(function () {
        ChatMessage::factory()->create([
            'chat_room_id' => null,
        ]);
    })->toThrow(QueryException::class);
});
