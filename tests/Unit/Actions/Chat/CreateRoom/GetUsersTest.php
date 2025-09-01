<?php

declare(strict_types=1);

use App\Actions\Chat\CreateRoom\GetUsers;
use App\Models\User;

use function Pest\Laravel\actingAs;

it('excludes current user, filters by keyword, excludes selected and orders by name', function () {
    $me = User::factory()->create(['name' => 'Zed']);
    actingAs($me);

    $alice = User::factory()->create(['name' => 'Alice Wonder']);
    $alicia = User::factory()->create(['name' => 'Alicia Keys']);
    $bob = User::factory()->create(['name' => 'Bob']);

    $action = app(GetUsers::class);

    $result = $action('Ali', $bob->id);

    expect($result->pluck('name')->all())
        ->toEqual(['Alice Wonder', 'Alicia Keys'])
        ->and($result->pluck('id')->contains($me->id))->toBeFalse()
        ->and($result->pluck('id')->contains($bob->id))->toBeFalse();
});
