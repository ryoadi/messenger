<?php

declare(strict_types=1);

use App\Actions\Chat\GroupRoom\GetUserCandidates;
use App\Models\ChatRoom;
use App\Models\User;

use function Pest\Laravel\actingAs;

it('returns users who are not members of the room, filters by keyword, excludes selected and orders by name', function () {
    $me = User::factory()->create(['name' => 'Zed']);
    actingAs($me);

    $room = ChatRoom::factory()->create();

    $alice = User::factory()->create(['name' => 'Alice Wonder']);
    $alicia = User::factory()->create(['name' => 'Alicia Keys']);
    $bob = User::factory()->create(['name' => 'Bob']);

    // Attach Alice to the room (member) so she should be excluded from candidates.
    $room->users()->attach($alice->id);

    $action = app(GetUserCandidates::class);

    // Search for "Ali", exclude Bob explicitly.
    $result = $action($room, 'Ali', $bob->id);

    expect($result->pluck('name')->all())
        ->toEqual(['Alicia Keys'])
        ->and($result->pluck('id')->contains($me->id))->toBeFalse()
        ->and($result->pluck('id')->contains($alice->id))->toBeFalse()
        ->and($result->pluck('id')->contains($bob->id))->toBeFalse();
});
