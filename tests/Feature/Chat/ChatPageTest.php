<?php

declare(strict_types=1);

use App\Models\ChatRoom;

it('serves the chat page for a given room id', function (): void {
    $user = \App\Models\User::factory()->create();
    $room = ChatRoom::factory()->create();

    $this->actingAs($user)
        ->get('/chat/'.$room->id)
        ->assertSuccessful();
});

it('returns 404 when the room does not exist', function (): void {
    $user = \App\Models\User::factory()->create();

    $this->actingAs($user)
        ->get('/chat/999999')
        ->assertNotFound();
});
