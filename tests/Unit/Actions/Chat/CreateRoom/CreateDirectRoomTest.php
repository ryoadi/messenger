<?php

declare(strict_types=1);

use App\Actions\Chat\CreateRoom\CreateDirectRoom;
use App\Models\ChatRoom;
use App\Models\Enums\ChatRoomType;
use App\Models\Enums\ChatRoomUserRole;
use App\Models\User;
use Illuminate\Validation\ValidationException;

it('prevents creating a direct room with yourself', function () {
    $me = User::factory()->create();

    $action = app(CreateDirectRoom::class);

    expect(fn () => $action($me->id, $me->id))
        ->toThrow(ValidationException::class);
});

it('returns existing direct room between the two users', function () {
    [$me, $other] = User::factory(2)->create();

    $existing = ChatRoom::factory()->direct()->create();
    $existing->users()->attach($me->id, ['role' => ChatRoomUserRole::Member]);
    $existing->users()->attach($other->id, ['role' => ChatRoomUserRole::Member]);

    $action = app(CreateDirectRoom::class);

    $room = $action($me->id, $other->id);

    expect($room->id)->toBe($existing->id);
});

it('creates a new direct room and attaches both users when not existing', function () {
    [$me, $other] = User::factory(2)->create();

    $action = app(CreateDirectRoom::class);

    $room = $action($me->id, $other->id);

    expect($room->type)->toBe(ChatRoomType::Direct)
        ->and($room->users()->count())->toBe(2)
        ->and($room->users()->whereKey([$me->id, $other->id])->count())->toBe(2);
});
