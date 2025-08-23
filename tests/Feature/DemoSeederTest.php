<?php

declare(strict_types=1);

use App\Models\ChatMessage;
use App\Models\ChatRoom;
use App\Models\User;
use Database\Seeders\ChatDemoSeeder;

it('seeds enough demo data for messenger', function () {
    // Run the demo seeder
    $this->seed(ChatDemoSeeder::class);

    // Basic counts
    expect(User::count())->toBeGreaterThan(10)
        ->and(ChatRoom::count())->toBeGreaterThan(10)
        ->and(ChatMessage::count())->toBeGreaterThan(100);

    // Check a room has multiple users and messages
    $room = ChatRoom::withCount(['users', 'messages'])->first();
    expect($room->users_count)->toBeGreaterThan(1)
        ->and($room->messages_count)->toBeGreaterThan(5);
});
