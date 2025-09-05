<?php

use App\Models\ChatRoom;
use App\Models\Enums\ChatRoomType;
use Livewire\Attributes\Locked;
use Livewire\Volt\Component;

new class extends Component {
    #[Locked]
    public ChatRoom $room;
}; ?>

<div class="flex flex-col gap-6 items-center">
    <flux:avatar circle :name="$room->title" size="xl"/>
    <flux:heading size="xl">{{ $room->title }}</flux:heading>

    <div class="w-full space-y-2">
        <flux:heading size="sm">{{ __('Members') }}</flux:heading>

        <div class="flex flex-col">
            @foreach($room->users as $user)
                <div class="flex items-center justify-between py-2" wire:key="member-{{ $user->id }}">
                    <div class="flex items-center gap-3 min-w-0">
                        <flux:avatar size="xs" circle :name="$user->name" />
                        <flux:text class="truncate">{{ $user->name }}</flux:text>
                    </div>

                    <flux:badge size="xs">{{ $user->pivot->role }}</flux:badge>
                </div>
            @endforeach
        </div>
    </div>
</div>
