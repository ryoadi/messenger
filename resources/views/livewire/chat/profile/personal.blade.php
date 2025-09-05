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
</div>
