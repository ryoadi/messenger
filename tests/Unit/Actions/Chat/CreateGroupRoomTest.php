<?php

declare(strict_types=1);

use App\Actions\Chat\CreateRoom\CreateGroupRoom;
use App\Actions\Chat\DTO\CreateGroupRoomInput;
use App\Models\Enums\ChatRoomType;
use App\Models\Enums\ChatRoomUserRole;
use App\Models\User;
use Illuminate\Validation\ValidationException;

it('validates at least two users and a name for group room', function () {
    $me = User::factory()->create();
    $u1 = User::factory()->create();

    $action = app(CreateGroupRoom::class);

    // less than 2 users
    $input = new CreateGroupRoomInput(
        name: '',
        currentUserId: $me->id,
        selectedUserIds: collect([$u1->id])
    );

    expect(fn () => $action($input))->toThrow(ValidationException::class);
});

it('creates a group room with owner and member attachments', function () {
    [$me, $alice, $bob] = User::factory(3)->create();

    $action = app(CreateGroupRoom::class);

    $input = new CreateGroupRoomInput(
        name: ' Project Team ',
        currentUserId: $me->id,
        selectedUserIds: collect([$alice->id, $bob->id, $me->id]) // includes self, should be auto-removed
    );

    $room = $action($input);

    expect($room->type)->toBe(ChatRoomType::Group)
        ->and($room->name)->toBe('Project Team')
        ->and($room->users()->wherePivot('role', ChatRoomUserRole::Owner->value)->pluck('users.id')->all())
        ->toEqual([$me->id])
        ->and($room->users()->wherePivot('role', ChatRoomUserRole::Member->value)->pluck('users.id')->sort()->values()->all())
        ->toEqualCanonicalizing([$alice->id, $bob->id]);
});
