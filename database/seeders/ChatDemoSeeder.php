<?php

namespace Database\Seeders;

use App\Models\Enums\ChatRoomUserRole;
use App\Models\ChatMessage;
use App\Models\ChatRoom;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;

class ChatDemoSeeder extends Seeder
{
    /**
     * Seed a rich set of demo data for the messenger.
     */
    public function run(): void
    {
        // Create a pool of users
        /** @var Collection<int, User> $users */
        $users = User::query()->count() >= 20
            ? User::query()->inRandomOrder()->take(20)->get()
            : User::factory()->count(20)->create();

        // Seed direct chat rooms (pairwise)
        for ($i = 0; $i < 20; $i++) {
            [$u1, $u2] = $users->random(2)->values();

            /** @var ChatRoom $room */
            $room = ChatRoom::factory()
                ->direct()
                ->create([
                    // For direct chats, name is typically null; enforce type rules in model
                    'name' => null,
                ]);

            // Attach exactly two users with roles
            $room->users()->attach($u1->id, ['role' => ChatRoomUserRole::Owner]);
            $room->users()->attach($u2->id, ['role' => ChatRoomUserRole::Member]);

            $this->seedMessages($room, collect([$u1, $u2]));
        }

        // Seed group chat rooms
        for ($i = 0; $i < 8; $i++) {
            // 1 owner + 2-6 members (3-7 total)
            $members = $users->random(random_int(3, 7))->values();
            $owner = $members->first();

            /** @var ChatRoom $room */
            $room = ChatRoom::factory()
                ->group()
                ->create([
                    // Ensure non-empty group name to satisfy validation in model
                    'name' => fake()->unique()->words(3, true),
                ]);

            $room->users()->attach($owner->id, ['role' => ChatRoomUserRole::Owner]);
            foreach ($members->skip(1) as $member) {
                $room->users()->attach($member->id, ['role' => ChatRoomUserRole::Member]);
            }

            $this->seedMessages($room, $members);
        }
    }

    /**
     * Seed a realistic message history for the given room.
     *
     * @param  Collection<int, User>  $participants
     */
    protected function seedMessages(ChatRoom $room, Collection $participants): void
    {
        $messageCount = random_int(15, 40);
        $start = Carbon::now()->subDays(30);
        $cursor = (clone $start);

        for ($i = 0; $i < $messageCount; $i++) {
            /** @var User $author */
            $author = $participants->random();
            $cursor = $cursor->copy()->addMinutes(random_int(5, 120));

            ChatMessage::query()->create([
                'chat_room_id' => $room->id,
                'user_id' => $author->id,
                'content' => fake()->realTextBetween(20, 120),
                'created_at' => $cursor,
                'updated_at' => $cursor,
            ]);
        }
    }
}
