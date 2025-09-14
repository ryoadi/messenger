<?php

declare(strict_types=1);

use App\Models\ChatRoom;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Volt\Volt;

use function Pest\Laravel\actingAs;

uses(RefreshDatabase::class);

it('allows room owner to add members via volt component', function () {
    $owner = User::factory()->create();
    $candidate = User::factory()->create();
    $room = ChatRoom::factory()->create();

    // attach owner as owner role on pivot
    $room->users()->attach($owner->id, ['role' => 'owner']);

    actingAs($owner);

    Volt::test('chat.profile.group', ['room' => $room])
        ->set('selectedCandidates', [$candidate->id])
        ->call('submitAddMembers')
        ->assertHasNoErrors();

    expect($room->fresh()->users->pluck('id')->contains($candidate->id))->toBeTrue();
});
