<?php

declare(strict_types=1);

use App\Actions\Chat\GetRooms;
use App\Actions\Chat\DTO\ListRoomsFilter;
use App\Models\Enums\ChatRoomType;
use App\Models\Enums\ChatRoomUserRole;
use App\Models\ChatRoom;
use App\Models\User;

it('lists only rooms for the user', function () {
    $me = User::factory()->create();

    $mine = ChatRoom::factory()->group()->create(['name' => 'Alpha']);
    $mine->users()->attach($me, ['role' => ChatRoomUserRole::Owner]);

    $others = ChatRoom::factory()->group()->create(['name' => 'Beta']);

    $action = app(GetRooms::class);
    $result = $action(new ListRoomsFilter(
        userId: $me->id,
    ));

    expect($result->pluck('id'))
        ->toContain($mine->id)
        ->not->toContain($others->id);
});

it('filters by type and searches correctly', function () {
    $me = User::factory()->create();

    $direct = ChatRoom::factory()->direct()->create();
    $alice = User::factory()->create(['name' => 'Alice']);
    $direct->users()->sync([
        $me->id => ['role' => ChatRoomUserRole::Member],
        $alice->id => ['role' => ChatRoomUserRole::Member],
    ]);

    $group = ChatRoom::factory()->group()->create(['name' => 'Team Rocket']);
    $group->users()->attach($me, ['role' => ChatRoomUserRole::Owner]);

    $action = app(GetRooms::class);

    // Search by other user name in direct
    $resultDirect = $action(new ListRoomsFilter(
        userId: $me->id,
        type: ChatRoomType::Direct,
        keyword: 'Ali',
    ));
    expect($resultDirect->pluck('id'))->toContain($direct->id);

    // Search by group title
    $resultGroup = $action(new ListRoomsFilter(
        userId: $me->id,
        type: ChatRoomType::Group,
        keyword: 'Rocket',
    ));
    expect($resultGroup->pluck('id'))->toContain($group->id);
});

it('orders by updated_at desc', function () {
    $me = User::factory()->create();

    $older = ChatRoom::factory()->group()->create(['name' => 'Old Group', 'updated_at' => now()->subDay()]);
    $older->users()->attach($me, ['role' => ChatRoomUserRole::Owner]);

    $newer = ChatRoom::factory()->group()->create(['name' => 'New Group', 'updated_at' => now()]);
    $newer->users()->attach($me, ['role' => ChatRoomUserRole::Owner]);

    $action = app(GetRooms::class);

    $result = $action(new ListRoomsFilter(
        userId: $me->id,
    ));

    expect($result->first()->id)->toBe($newer->id);
});
