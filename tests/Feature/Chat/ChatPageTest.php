<?php

declare(strict_types=1);

use App\Models\Enums\ChatRoomUserRole;
use App\Models\ChatRoom;

use function Pest\Laravel\actingAs;
use function Pest\Laravel\get;

it('serves the chat page for a given room id when the user is a member', function (): void {
    $user = \App\Models\User::factory()->create();
    $room = ChatRoom::factory()->create();

    // Attach the user as a member so authorization passes
    $room->users()->attach($user->getKey(), ['role' => ChatRoomUserRole::Member->value]);

    actingAs($user);
    get('/chat/'.$room->id)->assertSuccessful();
});

it('returns 404 when the room does not exist', function (): void {
    $user = \App\Models\User::factory()->create();

    actingAs($user);
    get('/chat/999999')->assertNotFound();
});
