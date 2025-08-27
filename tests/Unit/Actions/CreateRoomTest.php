<?php

declare(strict_types=1);

use App\Actions\Chat\CreateRoom;
use App\Models\ChatRoom;
use App\Models\Enums\ChatRoomType;
use App\Models\Enums\ChatRoomUserRole;
use App\Models\User;
use Illuminate\Validation\ValidationException;

it('creates a direct room when none exists and reuses existing direct room', function () {
    $action = new CreateRoom();

    $me = User::factory()->create();
    $other = User::factory()->create();

    // First call creates
    $room = $action($me->id, [$other->id], '');
    expect($room)->toBeInstanceOf(ChatRoom::class)
        ->and($room->type)->toBe(ChatRoomType::Direct)
        ->and($room->users()->count())->toBe(2);

    // Second call returns the same room
    $again = $action($me->id, [$other->id], '');
    expect($again->id)->toBe($room->id);
});

it('requires group title when selecting 2 or more users', function () {
    $action = new CreateRoom();

    $me = User::factory()->create();
    $u1 = User::factory()->create();
    $u2 = User::factory()->create();

    expect(fn () => $action($me->id, [$u1->id, $u2->id], ''))
        ->toThrow(ValidationException::class, 'Group chat room title is required.');

    $room = $action($me->id, [$u1->id, $u2->id], 'Team Alpha');
    expect($room->type)->toBe(ChatRoomType::Group)
        ->and($room->name)->toBe('Team Alpha')
        ->and($room->users()->count())->toBe(3);
});

it('does not allow selecting self only', function () {
    $action = new CreateRoom();

    $me = User::factory()->create();

    expect(fn () => $action($me->id, [$me->id], ''))
        ->toThrow(ValidationException::class, 'Please select at least one user.');
});

it('rejects non-empty title for direct room', function () {
    $action = new CreateRoom();

    $me = User::factory()->create();
    $other = User::factory()->create();

    expect(fn () => $action($me->id, [$other->id], 'Not empty'))
        ->toThrow(ValidationException::class, 'Title must be empty for a direct room.');
});
