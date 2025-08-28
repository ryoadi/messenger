<?php

declare(strict_types=1);

use App\Actions\Chat\CreateRoom\GetUsers;
use App\Models\ChatRoom;
use App\Models\Enums\ChatRoomType;
use App\Models\User;
use Illuminate\Database\Eloquent\Collection as EloquentCollection;
use Livewire\Features\SupportTesting\Testable;
use Livewire\Volt\Volt;

use function Pest\Laravel\actingAs;
use function Pest\Laravel\mock;

it('prevents selecting self', function () {
    $me = User::factory()->create();
    User::factory()->count(10)->create();

    actingAs($me);
    Volt::test('chat.create-room')
        ->call('selectUser', $me->id, $me->name)
        ->assertSet('selectedUsers', []);
});

it('can add and remove selected users', function () {
    $me = User::factory()->create();
    [$alice, $bob] = User::factory(2)->create();

    actingAs($me);
    Volt::test('chat.create-room')
        ->call('selectUser', $alice->id, $alice->name)
        ->call('selectUser', $bob->id, $bob->name)
        ->assertSet('selectedUsers', [
            $alice->id => $alice->name,
            $bob->id => $bob->name,
        ])
        ->call('removeSelectedUser', $alice->id)
        ->assertSet('selectedUsers', [
            $bob->id => $bob->name,
        ]);
});

it('computes users via GetUsers with keyword and excludes selected', function () {
    $me = User::factory()->create();
    User::factory()->create();
    $excluded = User::factory()->create();
    $searched = User::factory()->create(['name' => 'searched']);

    actingAs($me);
    Volt::test('chat.create-room')
        ->set('keyword', 'searched')
        ->set('selectedUsers', [
            $excluded->id => $excluded->name,
        ])
        ->tap(fn (Testable $test) =>
            expect($test->get('users')->contains('id', $searched->id))->toBeTrue()
                ->and($test->get('users'))->toHaveCount(1),
        );
});

it('creates a direct room when exactly one user is selected', function () {
    $me = User::factory()->create();
    $other = User::factory()->create();

    actingAs($me);
    $test = Volt::test('chat.create-room')
        ->set('selectedUsers', [
            $other->id => $other->name,
        ])
        ->call('create')
        ->assertSet('name', '')
        ->assertSet('keyword', '')
        ->assertSet('selectedUsers', []);

    $room = ChatRoom::latest()->with('users')->first();
    expect($room->type)->toBe(ChatRoomType::Direct)
        ->and($room->users->contains('id', $me->id))->toBeTrue()
        ->and($room->users->contains('id', $other->id))->toBeTrue();
});

it('creates a group room when multiple users are selected', function () {
    $me = User::factory()->create();
    [$alice, $bob] = User::factory(2)->create();

    actingAs($me);
    Volt::test('chat.create-room')
        ->set('name', 'My Awesome Group')
        ->set('selectedUsers', [
            $alice->id => $alice->name,
            $bob->id => $bob->name,
        ])
        ->call('create')
        ->assertSet('name', '')
        ->assertSet('keyword', '')
        ->assertSet('selectedUsers', []);

    $room = ChatRoom::latest()->with('users')->first();
    expect($room->type)->toBe(ChatRoomType::Group)
        ->and($room->users->contains('id', $me->id))->toBeTrue()
        ->and($room->users->contains('id', $alice->id))->toBeTrue()
        ->and($room->users->contains('id', $bob->id))->toBeTrue()
        ->and($room->users)->toHaveCount(3);
});
